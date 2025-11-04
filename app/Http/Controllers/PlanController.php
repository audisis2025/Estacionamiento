<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PlanController extends Controller
{
    public function create()
    {
        return view('admin.plans.create');
    }

    public function index()
    {
        $plans = Plan::orderBy('type')->orderBy('price')->get();
        return view('admin.plans.index', compact('plans'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
                'type' => ['required', Rule::in(['parking', 'user'])],
                'name' => [
                    'required',
                    'string',
                    'max:60',
                    Rule::unique('plans', 'name')
                        ->where(fn($query) => $query->where('type', $request->input('type'))),
                ],

                'price'          => ['required', 'numeric', 'min:0'],
                'duration_days'  => ['required', 'integer', 'min:1'],
                'description'    => ['required', 'string', 'max:255'],
            ],[
                'required' => 'El campo :attribute es obligatorio.',
                'string'   => 'El campo :attribute debe contener texto.',
                'numeric'  => 'El campo :attribute debe ser numérico.',
                'integer'  => 'El campo :attribute debe ser un número entero.',
                'max'      => 'El campo :attribute no debe exceder :max caracteres.',
                'min'      => 'El campo :attribute debe ser al menos :min.',
                'in'       => 'El valor seleccionado en :attribute no es válido.',
                'name.unique' => 'Ya existe un plan con ese nombre para el tipo seleccionado.',
            ],[
                'type'          => 'tipo de plan',
                'name'          => 'nombre del plan',
                'price'         => 'precio',
                'duration_days' => 'duración en días',
                'description'   => 'descripción',
            ]
        );

        Plan::create($validated);

        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Plan creado!',
            'text' => 'El plan ha sido creado correctamente.',
        ]);

        return back();
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate(
            [
                'type'           => ['required', Rule::in(['parking','user'])],
                'name'           => [
                    'required','string','max:60',
                    Rule::unique('plans','name')
                        ->where(fn($q) => $q->where('type', $request->type))
                        ->ignore($plan->id),
                ],
                'price'          => ['required','numeric','min:0'],
                'duration_days'  => ['required','integer','min:1'],
                'description'    => ['required','string','max:255'],
            ]
        );

        $plan->update($validated);

        return redirect()
            ->route('admin.plans.index')
            ->with('swal', [
                'icon'  => 'success',
                'title' => '¡Plan actualizado!',
                'text'  => 'Los cambios se guardaron correctamente.',
            ]);
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return back();
    }
}
