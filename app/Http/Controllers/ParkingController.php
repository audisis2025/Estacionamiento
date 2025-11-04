<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Parking;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParkingController extends Controller
{
    public function create()
    {
        if (Auth::user()->parking) {
            return redirect()->route('parking.edit');
        }

        return view('user.parking.create', [
            'days' => Day::orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateParking($request);
        $this->normalizeScheduleTimes($request); 
        $this->validateSchedules($request);
        $parking = Parking::create([
            'id_user'              => Auth::id(),
            'name'                 => $data['name'],
            'latitude_coordinate'  => $data['lat'],
            'longitude_coordinate' => $data['lng'],
            'type'                 => (int) $data['type'],
            'price'                => $data['price'] ?? 0,
        ]);

        $this->saveSchedules($parking, $request);

        return redirect()
            ->route('parking.edit')
            ->with('swal', [
                'icon'  => 'success',
                'title' => '¡Estacionamiento creado!',
                'text'  => 'El estacionamiento y su horario se guardaron correctamente.',
            ]);
    }

    public function edit()
    {
        $parking = Auth::user()->parking;
        if (!$parking) {
            return redirect()->route('parking.create');
        }

        return view('user.parking.edit', [
            'parking' => $parking,
            'days'    => Day::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $parking = Auth::user()->parking;
        if (!$parking) {
            return redirect()->route('parking.create');
        }

        $data = $this->validateParking($request);
        $this->normalizeScheduleTimes($request); 
        $this->validateSchedules($request);
        $parking->update([
            'name'                 => $data['name'],
            'latitude_coordinate'  => $data['lat'],
            'longitude_coordinate' => $data['lng'],
            'type'                 => (int) $data['type'],
            'price'                => $data['price'] ?? 0,
        ]);

        $this->saveSchedules($parking, $request);

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => '¡Actualizado!',
            'text'  => 'Estacionamiento y horario actualizados correctamente.',
        ]);
    }

    protected function validateParking(Request $request)
    {
        return $request->validate([
            'name'  => ['required', 'string', 'max:30'],
            'lat'   => ['required', 'numeric', 'between:-90,90'],
            'lng'   => ['required', 'numeric', 'between:-180,180'],
            'type'  => ['required', 'integer', 'in:0,1'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);
    }

    protected function saveSchedules(Parking $parking, Request $request)
    {
        $rows = $request->input('schedules', []);
        $sameForAll = $request->boolean('same_schedule');

        // Si usan horario global, replicar a todos los días
        if ($sameForAll && isset($rows['all'])) {
            $globalSchedule = $rows['all'];
            $open  = $globalSchedule['open'] ?? null;
            $close = $globalSchedule['close'] ?? null;

            // Validar que ambas horas estén presentes
            if (!$open || !$close) {
                return back()->withErrors([
                    'schedules.all.open' => 'Debes especificar hora de apertura y cierre.'
                ])->withInput();
            }

            // Validar que cierre sea mayor que apertura
            if ($open >= $close) {
                return back()->withErrors([
                    'schedules.all.close' => 'La hora de cierre debe ser mayor que la de apertura.'
                ])->withInput();
            }

            // Eliminar la clave 'all' y replicar a todos los días
            unset($rows['all']);

            foreach (Day::orderBy('id')->get() as $day) {
                $rows[$day->id] = [
                    'open' => $open,
                    'close' => $close,
                    'closed' => false // Forzar que estén abiertos
                ];
            }
        }

        // Procesar cada día
        foreach ($rows as $dayId => $row) {
            // Ignorar claves no numéricas por seguridad
            if (!is_numeric($dayId)) {
                continue;
            }

            $dayId = (int) $dayId;

            // Verificar que el día existe
            if (!Day::find($dayId)) {
                continue;
            }

            $closed = !empty($row['closed']);
            $open   = $row['open']  ?? null;
            $close  = $row['close'] ?? null;

            // Si está cerrado o faltan horarios, eliminar el registro
            if ($closed || !$open || !$close) {
                Schedule::where('id_parking', $parking->id)
                    ->where('id_day', $dayId)
                    ->delete();
                continue;
            }

            // Validar que cierre > apertura
            if ($open >= $close) {
                // Solo validar si NO es horario global (ya se validó arriba)
                if (!$sameForAll) {
                    return back()->withErrors([
                        "schedules.$dayId.close" => "La hora de cierre debe ser mayor que la de apertura."
                    ])->withInput();
                }
                continue;
            }

            // Crear o actualizar el horario
            Schedule::updateOrCreate(
                [
                    'id_parking' => $parking->id,
                    'id_day' => $dayId
                ],
                [
                    'opening_time' => $open,
                    'closing_time' => $close
                ]
            );
        }

        return null; // Éxito
    }

    protected function normalizeScheduleTimes(Request $request): void
    {
        $schedules = $request->input('schedules', []);

        $norm = function ($t) {
            if ($t === null || $t === '') return $t;
            $t = trim((string)$t);

            // convierte "a. m." / "p. m." → AM/PM
            $t = preg_replace('/\s*a\.?\s*m\.?/i', ' AM', $t);
            $t = preg_replace('/\s*p\.?\s*m\.?/i', ' PM', $t);

            // si viene H:i:s → recórtalo
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $t)) {
                return substr($t, 0, 5); // H:i
            }

            // intenta parsear cualquier variante (09:00, 9:00 PM, etc.) y devuélvela en 24h H:i
            try {
                return Carbon::parse($t)->format('H:i');
            } catch (\Throwable $e) {
                return $t; // deja que la validación falle si no se puede
            }
        };

        // global
        if (isset($schedules['all'])) {
            $schedules['all']['open']  = $norm($schedules['all']['open']  ?? null);
            $schedules['all']['close'] = $norm($schedules['all']['close'] ?? null);
        }
        // por día
        foreach ($schedules as $k => $row) {
            if ($k === 'all' || !is_array($row)) continue;
            $schedules[$k]['open']  = $norm($row['open']  ?? null);
            $schedules[$k]['close'] = $norm($row['close'] ?? null);
        }

        $request->merge(['schedules' => $schedules]);
    }


    protected function validateSchedules(Request $request)
    {
        $days = \App\Models\Day::orderBy('id')->get();

        $rules = [];

        if ($request->boolean('same_schedule')) {
            // Horario global
            $rules['schedules.all.open']  = ['required', 'date_format:H:i'];
            $rules['schedules.all.close'] = ['required', 'date_format:H:i', 'after:schedules.all.open'];
        } else {
            // Por día
            foreach ($days as $d) {
                $prefix = "schedules.{$d->id}";

                // si "cerrado" = 1, excluye las horas de validación
                $rules["{$prefix}.closed"] = ['nullable', 'in:0,1'];

                $rules["{$prefix}.open"]  = [
                    'exclude_if:' . $prefix . '.closed,1',
                    'required',          // si NO está cerrado, es requerido
                    'date_format:H:i',
                ];
                $rules["{$prefix}.close"] = [
                    'exclude_if:' . $prefix . '.closed,1',
                    'required',          // si NO está cerrado, es requerido
                    'date_format:H:i',
                    "after:{$prefix}.open", // cierre > apertura
                ];
            }
        }

        $messages = [
            'required' => 'El campo :attribute es obligatorio.',
            'date_format' => 'El campo :attribute debe tener el formato HH:MM.',
            'after' => 'La hora de cierre debe ser mayor que la de apertura.',
            'in' => 'Valor inválido.',
        ];

        $attributes = [
            'schedules.all.open'  => 'apertura (todos los días)',
            'schedules.all.close' => 'cierre (todos los días)',
        ];
        foreach ($days as $d) {
            $attributes["schedules.{$d->id}.open"]  = "apertura ({$d->name})";
            $attributes["schedules.{$d->id}.close"] = "cierre ({$d->name})";
        }

        $request->validate($rules, $messages, $attributes);
    }
}
