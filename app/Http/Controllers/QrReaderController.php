<?php

namespace App\Http\Controllers;

use App\Models\QrReader;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QrReaderController extends Controller
{
    public function index()
    {
        $parking = auth()->user()->parking;
        
        if (!$parking) 
        {
            return redirect()->route('parking.edit')->with('swal', [
                'icon'  => 'warning',
                'title' => 'Configura tu estacionamiento',
                'text'  => 'Debes dar de alta tu estacionamiento antes de administrar lectores.',
            ]);
        }

        $readers = $parking->qrReaders()->latest('id')->get();

        return view('user.qr_readers.index', compact('readers'));
    }

    public function create()
    {
        return view('user.qr_readers.create');
    }

    public function store(Request $request)
    {
        try 
        {
            $parking = auth()->user()->parking;

            $data = $request->validate([
                'serial_number' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('qr_readers', 'serial_number')->where(fn($q) => $q->where('id_parking', $parking->id)),
                ],
                'sense' => ['required', 'integer', Rule::in([0, 1, 2])],
            ]);

            $parking->qrReaders()->create([
                'serial_number' => $data['serial_number'],
                'sense'         => $data['sense'],
            ]);

            return redirect()->route('parking.qr-readers.index')->with('swal', [
                'icon'  => 'success',
                'title' => 'Lector creado',
                'text'  => 'El lector QR se registró correctamente.',
            ]);

        } catch (ValidationException $e) 
        {
            // Capturar el primer error para mostrar en SweetAlert
            $firstError = collect($e->errors())->first()[0] ?? 'Error de validación';
            
            return back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Error de validación',
                'text'  => $firstError,
            ])->withInput();
        }
    }

    public function edit(QrReader $reader)
    {
        $this->ensureOwnership($reader);

        return view('user.qr_readers.edit', compact('reader'));
    }

    public function update(Request $request, QrReader $reader)
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
                        ->where(fn($q) => $q->where('id_parking', $parking->id))
                        ->ignore($reader->id),
                ],
                'sense' => ['required', 'integer', Rule::in([0, 1, 2])],
            ]);

            $reader->update($data);

            return redirect()->route('parking.qr-readers.index')->with('swal', [
                'icon'  => 'success',
                'title' => 'Lector actualizado',
                'text'  => 'Se guardaron los cambios correctamente.',
            ]);

        } catch (ValidationException $e) 
        {
            $firstError = collect($e->errors())->first()[0] ?? 'Error de validación';
            
            return back()->with('swal', [
                'icon'  => 'error',
                'title' => 'Error de validación',
                'text'  => $firstError,
            ])->withInput();
        }
    }

    public function destroy(QrReader $reader)
    {
        $this->ensureOwnership($reader);

        $reader->delete();

        return redirect()->route('parking.qr-readers.index')->with('swal', [
            'icon'  => 'success',
            'title' => 'Lector eliminado',
            'text'  => 'El lector QR fue eliminado.',
        ]);
    }

    private function ensureOwnership(QrReader $reader): void
    {
        $parking = auth()->user()->parking;
        abort_unless($parking && $reader->id_parking === $parking->id, 403, 'No autorizado.');
    }
}