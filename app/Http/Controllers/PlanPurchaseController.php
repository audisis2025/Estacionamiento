<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanPurchaseController extends Controller
{
    public function choose()
    {
        $plans = \App\Models\Plan::where('type','parking')->orderBy('price')->orderBy('duration_days')->get();
        return view('user.plans.choose', compact('plans'));
    }

    public function checkout(Plan $plan)
    {
        abort_unless($plan->type === 'parking', 404);
        return view('user.plans.checkout', compact('plan'));
    }

    public function pay(Request $request, Plan $plan)
    {
        abort_unless($plan->type === 'parking', 404);

        // === SANDBOX ===
        // Aquí iría la integración real (Stripe, PayPal...). Simulamos éxito.
        $user = $request->user();

        $user->id_plan = $plan->id;
        $user->end_date = now()->addDays($plan->duration_days); // fin de vigencia
        // (Opcional) acumula gasto total si usas users.amount
        // $user->amount = (int)$user->amount + (int)round($plan->price);
        $user->save();

        return redirect()->route('dashboard')
            ->with('success', '¡Plan activado! Vigente hasta: ' . $user->end_date);
    }
    
}
