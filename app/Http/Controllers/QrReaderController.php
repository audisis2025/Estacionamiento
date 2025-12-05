<?php
/*
* Nombre de la clase         : QrReaderController.php
* Descripción de la clase    : Controlador que maneja la lectura de códigos QR para el usuario autenticado.
* Fecha de creación          : 05/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 06/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0
* Fecha de mantenimiento     :
* Folio de mantenimiento     :
* Descripción del mantenimiento :
* Responsable                :
* Revisor                    :
*/

namespace App\Http\Controllers;

use App\Models\QrReader;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class QrReaderController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $parking = auth()->user()->parking;

        if (!$parking)
        {
            return redirect()
                ->route('parking.edit')
                ->with('swal', [
                    'icon'  => 'warning',
                    'title' => 'Configura tu estacionamiento',
                    'text'  => 'Debes dar de alta tu estacionamiento antes de administrar lectores.'
                ]);
        }

        $readers = $parking->qrReaders()
            ->latest('id')
            ->get();

        return view('user.qr_readers.index', compact('readers'));
    }

    public function create(): View
    {
        return view('user.qr_readers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        try
        {
            $parking = auth()->user()->parking;

            $data = $request->validate([
                'serial_number' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('qr_readers', 'serial_number')
                        ->where( fn ($query) => $query->where('id_parking', $parking->id))
                ],
                'sense' => [
                    'required',
                    'integer',
                    Rule::in([
                        0,
                        1,
                        2
                    ])
                ]
            ]);

            $parking->qrReaders()->create(['serial_number' => $data['serial_number'],'sense' => $data['sense']]);

            return redirect()
                ->route('parking.qr-readers.index')
                ->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Lector creado',
                    'text'  => 'El lector QR se registró correctamente.'
                ]);
        } catch (ValidationException $exception)
        {
            $firstError = collect($exception->errors())
                ->first()[0] ?? 'Error de validación';

            return back()
                ->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Error de validación',
                    'text'  => $firstError
                ])
                ->withInput();
        }
    }

    public function edit(QrReader $reader): View
    {
        $this->ensureOwnership($reader);

        return view('user.qr_readers.edit', compact('reader'));
    }

    public function update(Request $request, QrReader $reader): RedirectResponse
    {
        try
        {
            $this->ensureOwnership($reader);

            $parking = auth()->user()->parking;

            $data = $request->validate([
                'serial_number' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('qr_readers', 'serial_number')
                        ->where(fn ($query) => $query->where('id_parking', $parking->id))
                        ->ignore($reader->id)
                ],
                'sense' => [
                    'required',
                    'integer',
                    Rule::in([
                        0, 
                        1, 
                        2
                    ])
                ]
            ]);

            $reader->update($data);

            return redirect()
                ->route('parking.qr-readers.index')
                ->with('swal', [
                    'icon' => 'success',
                    'title' => 'Lector actualizado',
                    'text' => 'Se guardaron los cambios correctamente.'
                ]);
        } catch (ValidationException $exception)
        {
            $firstError = collect($exception->errors())
                ->first()[0] ?? 'Error de validación';

            return back()
                ->with('swal', [
                    'icon' => 'error',
                    'title' => 'Error de validación',
                    'text' => $firstError
                ])
                ->withInput();
        }
    }

    public function destroy(QrReader $reader): RedirectResponse
    {
        $this->ensureOwnership($reader);

        try 
        {
            $reader->delete();

            return redirect()
                ->route('parking.qr-readers.index')
                ->with('swal', [
                    'icon'  => 'success',
                    'title' => 'Lector eliminado',
                    'text'  => 'El lector QR fue eliminado.',
                ]);
        }
        catch (QueryException $e) 
        {
            if ($e->getCode() === '23000') 
            {
                return redirect()
                    ->route('parking.qr-readers.index')
                    ->with('swal', [
                        'icon'  => 'error',
                        'title' => 'No se puede eliminar',
                        'text'  => 'Este lector tiene registros de entradas/salidas.',
                    ]);
            }

            throw $e;
        }
    }

    private function ensureOwnership(QrReader $reader): void
    {
        $parking = auth()->user()->parking;

        abort_unless(
            $parking && $reader->id_parking === $parking->id,
            403,
            'No autorizado.'
        );
    }
}
