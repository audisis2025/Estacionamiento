<?php

namespace App\Http\Controllers;

use App\Models\QrReader;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QrReaderController extends Controller
{
    public function index()
    {
        $parking = auth()->user()->parking;
        // Si por alguna razón no hay parking (middleware debería evitarlo)
        if (!$parking) {
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
        $parking = auth()->user()->parking;

        $data = $request->validate([
            'serial_number' => [
                'required',
                'string',
                'max:50',
                // único por estacionamiento
                Rule::unique('qr_readers', 'serial_number')->where(fn($q) => $q->where('id_parking', $parking->id)),
            ],
            'sense' => ['required', 'integer', Rule::in([0, 1, 2])],
        ]);

        $parking->qrReaders()->create([
            'serial_number' => $data['serial_number'],
            'sense'         => $data['sense'],
            // id_parking se rellena por la relación
        ]);

        return redirect()->route('parking.qr-readers.index')->with('swal', [
            'icon'  => 'success',
            'title' => 'Lector creado',
            'text'  => 'El lector QR se registró correctamente.',
        ]);
    }

    public function edit(QrReader $reader)
    {
        $this->ensureOwnership($reader);

        return view('user.qr_readers.edit', compact('reader'));
    }

    public function update(Request $request, QrReader $reader)
    {
        $this->ensureOwnership($reader);

        $parking = auth()->user()->parking;

        $data = $request->validate([
            'serial_number' => [
                'required',
                'string',
                'max:50',
                // único por estacionamiento, ignorando el propio ID
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

    /**
     * Asegura que el lector pertenezca al estacionamiento del usuario.
     */
    private function ensureOwnership(QrReader $reader): void
    {
        $parking = auth()->user()->parking;
        abort_unless($parking && $reader->id_parking === $parking->id, 403, 'No autorizado.');
    }
}
