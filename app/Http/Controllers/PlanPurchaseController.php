<?php
/*
* Nombre de la clase         : PlanPurchaseController.php
* Descripción de la clase    : Controlador que maneja la compra y activación de planes para el usuario autenticado.
* Fecha de creación          : 04/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 05/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0
* Fecha de mantenimiento     :
* Folio de mantenimiento     :
* Descripción del mantenimiento :
* Responsable                :
* Revisor                    :
*/

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PlanPurchaseController extends Controller
{
    public function choose(): View
    {
        $plans = Plan::where('type', 'parking')
            ->orderBy('price')
            ->orderBy('duration_days')
            ->get();

        return view('user.plans.choose', compact('plans'));
    }

    public function checkout(Plan $plan): View
    {
        abort_unless($plan->type === 'parking', 404);

        return view('user.plans.checkout', compact('plan'));
    }

    public function pay(Request $request, Plan $plan): RedirectResponse
    {
        abort_unless($plan->type === 'parking', 404);

        $user = $request->user();

        $user->id_plan = $plan->id;
        $user->end_date = now()->addDays($plan->duration_days);
        $user->save();

        return redirect()
            ->route('dashboard')
            ->with('success', '¡Plan activado! Vigente hasta: ' . $user->end_date);
    }
}
