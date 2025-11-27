<?php
/*
* Nombre de la clase         : PlanController.php
* Descripción de la clase    : Controlador que maneja las interacciones con los planes de usuario.
* Fecha de creación          : 03/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 03/11/2025
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
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PlanController extends Controller
{
    public function index() : View
    {
        $plans = Plan::orderBy('type')->orderBy('price')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate(
            [
                'name'=> [
                    'required',
                    'string',
                    'max:60',
                    Rule::unique('plans', 'name')
                        ->where(fn($q) => $q->where('type', $request->type))
                        ->ignore($plan->id)
                ],
                'price' => [
                    'required', 
                    'numeric', 
                    'min:0'
                ],
                'duration_days' => [
                    'required', 
                    'integer', 
                    'min:1'
                ],
                'description' => [
                    'required',
                    'string',
                    'max:255'
                ],
            ]
        );

        $plan->update($validated);

        return redirect()
            ->route('admin.plans.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => '¡Plan actualizado!',
                'text'  => 'Los cambios se guardaron correctamente.'
            ]);
    }
}
