<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayPalController extends Controller
{
    public function __construct(private PayPalService $paypal) {}

    public function create(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ]);

        $plan = Plan::where('id', $data['plan_id'])
            ->where('type', 'parking')
            ->firstOrFail();

        $reference = 'plan-' . $plan->id . '-user-' . $request->user()->id;
        $order = $this->paypal->createOrder((float)$plan->price, $reference);

        if (($order['status'] ?? 500) !== 201) {
            Log::error('PayPal CREATE error', [
                'plan_id' => $plan->id,
                'status' => $order['status'] ?? 500,
                'response' => $order['body'] ?? null
            ]);
            
            return response()->json([
                'message' => 'Error al crear la orden en PayPal',
                'details' => $order['body']['message'] ?? 'Error desconocido'
            ], 422);
        }

        return response()->json(['id' => $order['body']['id']]);
    }

    public function capture(Request $request, string $orderId)
    {
        // 1️⃣ Primero verificar el estado de la orden
        $orderInfo = $this->paypal->getOrder($orderId);
        
        if (($orderInfo['status'] ?? 500) !== 200) {
            Log::error('PayPal getOrder failed', [
                'orderId' => $orderId,
                'status' => $orderInfo['status'] ?? 500,
                'response' => $orderInfo['body'] ?? null
            ]);
            
            return response()->json([
                'message' => 'No se pudo verificar el estado de la orden',
                'debug_id' => $orderInfo['body']['debug_id'] ?? null
            ], 422);
        }

        $orderStatus = $orderInfo['body']['status'] ?? null;
        $orderBody = $orderInfo['body'];

        // 2️⃣ Validar que la orden esté en estado APPROVED
        if ($orderStatus !== 'APPROVED') {
            Log::warning('Order not in APPROVED state', [
                'orderId' => $orderId,
                'status' => $orderStatus,
                'order' => $orderBody
            ]);

            $message = match($orderStatus) {
                'COMPLETED' => 'Esta orden ya fue procesada anteriormente',
                'CREATED' => 'El pago aún no ha sido aprobado por el usuario',
                'VOIDED' => 'Esta orden fue cancelada',
                'SAVED' => 'La orden está guardada pero no aprobada',
                default => "Estado de orden inválido: {$orderStatus}"
            };

            return response()->json([
                'message' => $message,
                'status' => $orderStatus,
                'order_id' => $orderId
            ], 422);
        }

        // 3️⃣ Intentar capturar la orden
        $capture = $this->paypal->captureOrder($orderId);

        if (($capture['status'] ?? 500) !== 201) {
            $captureBody = $capture['body'] ?? [];
            
            Log::error('PayPal CAPTURE failed', [
                'orderId' => $orderId,
                'status' => $capture['status'] ?? 500,
                'debug_id' => $captureBody['debug_id'] ?? null,
                'name' => $captureBody['name'] ?? null,
                'message' => $captureBody['message'] ?? null,
                'details' => $captureBody['details'] ?? null,
                'full_response' => $captureBody
            ]);

            return response()->json([
                'message' => $captureBody['message'] ?? 'Error al capturar el pago',
                'debug_id' => $captureBody['debug_id'] ?? null,
                'details' => $captureBody['details'] ?? null
            ], 422);
        }

        $captureBody = $capture['body'] ?? [];
        $captureStatus = $captureBody['status'] ?? null;

        // 4️⃣ Verificar que la captura fue exitosa
        if ($captureStatus !== 'COMPLETED') {
            Log::warning('Capture not completed', [
                'orderId' => $orderId,
                'status' => $captureStatus,
                'response' => $captureBody
            ]);
            
            return response()->json([
                'message' => 'La captura del pago no se completó',
                'status' => $captureStatus
            ], 422);
        }

        // 5️⃣ Extraer y validar la referencia del plan
        $reference = data_get($captureBody, 'purchase_units.0.reference_id', '');
        
        if (!preg_match('/^plan-(\d+)-user-(\d+)$/', $reference, $matches)) {
            Log::error('Invalid reference in capture', [
                'orderId' => $orderId,
                'reference' => $reference,
                'capture' => $captureBody
            ]);
            
            return response()->json([
                'message' => 'Referencia de plan inválida'
            ], 422);
        }

        [, $planId, $userIdFromRef] = $matches;

        // 6️⃣ Validar el plan
        $plan = Plan::where('id', $planId)
            ->where('type', 'parking')
            ->first();

        if (!$plan) {
            Log::error('Plan not found in capture', [
                'orderId' => $orderId,
                'plan_id' => $planId,
                'reference' => $reference
            ]);
            
            return response()->json([
                'message' => 'Plan no encontrado'
            ], 404);
        }

        // 7️⃣ Verificar que el usuario coincida
        $user = $request->user();
        
        if ($user->id != $userIdFromRef) {
            Log::error('User mismatch in capture', [
                'orderId' => $orderId,
                'authenticated_user' => $user->id,
                'reference_user' => $userIdFromRef
            ]);
            
            return response()->json([
                'message' => 'Usuario no autorizado para esta transacción'
            ], 403);
        }

        // 8️⃣ Actualizar el usuario con el plan
        try {
            DB::transaction(function () use ($user, $plan) {
                $user->id_plan = $plan->id;
                $user->end_date = now()->addDays($plan->duration_days);
                $user->save();

                // Actualizar el balance del admin si existe
                $admin = User::where('phone_number', '7777777777')->first();
                if ($admin) {
                    $admin->amount = (float)($admin->amount ?? 0) + (float)$plan->price;
                    $admin->save();
                }
            });

            Log::info('Plan activated successfully', [
                'orderId' => $orderId,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'end_date' => $user->end_date
            ]);

            return response()->json([
                'message' => 'Pago completado exitosamente',
                'redirect' => route('dashboard')
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving plan to user', [
                'orderId' => $orderId,
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Error al activar el plan. Contacta soporte.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}