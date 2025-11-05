<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Services\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PayPalApiController extends Controller
{
    public function __construct(private PayPalService $paypal) {}

    /**
     * Crear una orden PayPal desde Flutter
     */
    public function create(Request $request)
    {
        $data = $request->validate([
            'plan_id' => ['required', 'integer', 'exists:plans,id'],
        ]);

        $plan = Plan::where('id', $data['plan_id'])
            ->where('type', 'user') // ✅ Cambiado a tipo usuario
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

        return response()->json([
            'status' => 'success',
            'order_id' => $order['body']['id']
        ]);
    }

    /**
     * Capturar y validar pago de PayPal
     */
    public function capture(Request $request, string $orderId)
    {
        $user = $request->user();

        // 1️⃣ Obtener información de la orden
        $orderInfo = $this->paypal->getOrder($orderId);
        if (($orderInfo['status'] ?? 500) !== 200) {
            return response()->json([
                'message' => 'No se pudo verificar la orden PayPal',
                'status' => $orderInfo['status'] ?? 500
            ], 422);
        }

        $status = $orderInfo['body']['status'] ?? 'UNKNOWN';
        if ($status !== 'APPROVED') {
            return response()->json([
                'message' => "La orden no está aprobada. Estado actual: $status",
                'status' => $status
            ], 422);
        }

        // 2️⃣ Capturar orden
        $capture = $this->paypal->captureOrder($orderId);
        if (($capture['status'] ?? 500) !== 201) {
            return response()->json([
                'message' => 'Error al capturar el pago',
                'details' => $capture['body'] ?? []
            ], 422);
        }

        $captureBody = $capture['body'];
        if (($captureBody['status'] ?? '') !== 'COMPLETED') {
            return response()->json([
                'message' => 'El pago no se completó correctamente',
                'status' => $captureBody['status'] ?? null
            ], 422);
        }

        // 3️⃣ Extraer referencia y validar
        $reference = data_get($captureBody, 'purchase_units.0.reference_id', '');
        if (!preg_match('/^plan-(\d+)-user-(\d+)$/', $reference, $matches)) {
            return response()->json([
                'message' => 'Referencia de pago inválida'
            ], 422);
        }

        [, $planId, $userId] = $matches;
        if ($userId != $user->id) {
            return response()->json([
                'message' => 'Usuario no autorizado para esta orden'
            ], 403);
        }

        // 4️⃣ Asignar plan al usuario
        $plan = Plan::find($planId);
        if (!$plan) {
            return response()->json(['message' => 'Plan no encontrado'], 404);
        }

        DB::transaction(function () use ($user, $plan) {
            $user->id_plan = $plan->id;
            $user->end_date = now()->addDays($plan->duration_days);
            $user->save();

            $admin = User::where('phone_number', '7777777777')->first();
            if ($admin) {
                $admin->amount = (float)($admin->amount ?? 0) + (float)$plan->price;
                $admin->save();
            }
        });

        Log::info('Plan activado via API', [
            'user' => $user->id,
            'plan' => $plan->id,
            'order' => $orderId,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pago completado exitosamente',
            'plan' => $plan,
            'user' => [
                'id' => $user->id,
                'plan_id' => $user->id_plan,
                'end_date' => $user->end_date,
            ]
        ]);
    }
}
