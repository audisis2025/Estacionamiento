<?php

namespace App\Http\Controllers;

use App\Models\ClientType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException; // <-- IMPORTANTE

class ClientTypeController extends Controller
{
    public function index()
    {
        $parking = auth()->user()->parking;

        if (!$parking) {
            return redirect()->route('parking.edit')->with('swal', [
                'icon'  => 'warning',
                'title' => 'Configura tu estacionamiento',
                'text'  => 'Debes registrar tu estacionamiento y su horario antes de administrar tipos de cliente.',
            ]);
        }

        $clientTypes = $parking->clientTypes()->orderByDesc('id')->get();

        return view('user.client_types.index', compact('clientTypes'));
    }

    public function create()
    {
        return view('user.client_types.create');
    }

    public function store(Request $request)
    {
        try {
            $parking = auth()->user()->parking;

            $data = $request->validate([
                'typename'      => [
                    'required',
                    'string',
                    'max:50',
                    // único por estacionamiento
                    Rule::unique('client_types', 'typename')
                        ->where(fn($q) => $q->where('id_parking', $parking->id)),
                ],
                'discount_type' => ['required', 'integer', Rule::in([0, 1])], // 0=% | 1=cantidad
                'amount'        => ['required', 'numeric', 'min:1'],
            ]);

            $parking->clientTypes()->create($data);

            return redirect()->route('parking.client-types.index')->with('swal', [
                'icon'  => 'success',
                'title' => 'Tipo creado',
                'text'  => 'El tipo de cliente se registró correctamente.',
            ]);

        } catch (ValidationException $e) {
            $allErrors = collect($e->errors())->flatten()->toArray();
            
            $errorList = '<ul style="text-align: left; margin-left: 20px;">';
            foreach ($allErrors as $error) {
                $errorList .= "<li>{$error}</li>";
            }
            $errorList .= '</ul>';

            return back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Errores en el formulario',
                'html'  => $errorList,
            ])->withInput();

        }
    }

    public function edit(ClientType $clientType)
    {
        $this->ensureOwnership($clientType);

        return view('user.client_types.edit', compact('clientType'));
    }

    public function update(Request $request, ClientType $clientType)
    {
        try {
            $this->ensureOwnership($clientType);

            $parking = auth()->user()->parking;

            $data = $request->validate([
                'typename'      => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('client_types', 'typename')
                        ->where(fn($q) => $q->where('id_parking', $parking->id))
                        ->ignore($clientType->id),
                ],
                'discount_type' => ['required', 'integer', Rule::in([0, 1])],
                'amount'        => ['required', 'numeric', 'min:1'],
            ]);

            $clientType->update($data);

            return redirect()->route('parking.client-types.index')->with('swal', [
                'icon'  => 'success',
                'title' => 'Tipo actualizado',
                'text'  => 'Los cambios se guardaron correctamente.',
            ]);

        } catch (ValidationException $e) {
            $allErrors = collect($e->errors())->flatten()->toArray();
            
            $errorList = '<ul style="text-align: left; margin-left: 20px;">';
            foreach ($allErrors as $error) {
                $errorList .= "<li>{$error}</li>";
            }
            $errorList .= '</ul>';

            return back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Errores en el formulario',
                'html'  => $errorList,
            ])->withInput();
        }
    }

    public function destroy(ClientType $clientType)
    {
        $this->ensureOwnership($clientType);

        $clientType->delete();

        return redirect()->route('parking.client-types.index')->with('swal', [
            'icon'  => 'success',
            'title' => 'Tipo eliminado',
            'text'  => 'El tipo de cliente fue eliminado.',
        ]);
    }

    private function ensureOwnership(ClientType $clientType): void
    {
        $parking = auth()->user()->parking;
        abort_unless($parking && $clientType->id_parking === $parking->id, 403, 'No autorizado.');
    }
}
