<?php
/*
* Nombre de la clase         : PayPalApiController.php
* Descripción de la clase    : Controlador para administrar la bandeja de entrada de los usuarios en relación a
                               los estacionamientos.
* Fecha de creación          : 05/11/2025
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 05/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 2.0
* Fecha de mantenimiento     : 10/01/2026
* Folio de mantenimiento     : L0026
* Tipo de mantenimiento      : Perfectivo
* Descripción del mantenimiento : Hacer uso correcto y manejo al crear una orden de pago de paypal para aplicacion movil desde backend
* Responsable                : Jonathan Diaz
* Revisor                    : Angel Davila
*/
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use App\Services\PayPalService;
use Illuminate\Support\Facades\Log;
 
class PayPalApiController extends Controller
{
    public function __construct(private PayPalService $paypal) {}
 
    public function create(Request $request)
    {
        $data = $request->validate([
            'type' => ['required', 'in:plan,wallet'],
            'plan_id' => ['required_if:type,plan', 'exists:plans,id'],
            'amount' => [
                'required_if:type,wallet', 
                'numeric', 
                'min:1'
            ]
        ]);
 
        $user = $request->user();
 
        if ($data['type'] === 'plan') 
        {
            $plan = Plan::where('id', $data['plan_id'])
                ->where('type', 'user')
                ->firstOrFail();
 
            $reference = "plan-{$plan->id}-user-{$user->id}";
            $price = (float) $plan->price;
        } else 
        {
            $reference = "wallet-{$user->id}-amount-{$data['amount']}";
            $price = (float) $data['amount'];
        }
 
        $order = $this->paypal->createOrder($price, $reference);
 
        if (($order['status'] ?? 500) !== 201) 
        {
            Log::error('PayPal CREATE error', $order);
            return response()->json(['message' => 'Error al crear la orden'], 422);
        }
        return response()->json(['order_id' => $order['body']['id'], 'approve_url' => collect($order['body']['links'])->firstWhere('rel', 'approve')['href'] ?? null]);
    }
 
    public function capture(Request $request, string $orderId)
    {
        $orderInfo = $this->paypal->getOrder($orderId);
 
        if (($orderInfo['status'] ?? 500) !== 200) 
        {
            return response()->json(['message' => 'Orden inválida'], 422);
        }
 
        if (($orderInfo['body']['status'] ?? null) !== 'APPROVED') 
        {
            return response()->json(['message' => 'Orden no aprobada'], 422);
        }
 
        $capture = $this->paypal->captureOrder($orderId);
 
        if (($capture['status'] ?? 500) !== 201) 
        {
            return response()->json(['message' => 'Error al capturar pago'], 422);
        }
 
        $reference = data_get($capture, 'body.purchase_units.0.reference_id');
 
        if (preg_match('/^plan-(\d+)-user-(\d+)$/', $reference, $m)) 
        {
            return $this->applyPlan($request->user(), $m[1]);
        }
 
        if (preg_match('/^wallet-(\d+)-amount-(\d+(\.\d+)?)$/', $reference, $m)) 
        {
            return $this->applyWallet($request->user(), (float) $m[2]);
        }
 
        return response()->json(['message' => 'Referencia inválida'], 422);
    }
 
    private function applyPlan($user, $planId)
    {
        $plan = Plan::where('id', $planId)
            ->where('type', 'user')
            ->firstOrFail();
 
        $user->id_plan = $plan->id;
        $user->end_date = now()->addDays($plan->duration_days);
        $user->save();
 
        return response()->json([
            'type' => 'plan',
            'message' => 'Plan activado',
            'end_date' => $user->end_date
        ]);
    }
 
    private function applyWallet($user, float $amount)
    {
        $user->increment('amount', $amount);
        return response()->json([
            'type' => 'wallet',
            'message' => 'Saldo recargado',
            'new_balance' => (float) $user->amount
        ]);
    }
}