<?php
/*
* Nombre de la clase         : ParkingController.php
* Descripción de la clase    : Controlador que maneja la creación y edición de estacionamientos para el usuario autenticado.
* Fecha de creación          : 05/11/2025
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

use App\Models\Day;
use App\Models\Parking;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ParkingController extends Controller
{
    /**
     * Muestra el formulario de creación de estacionamiento.
     *
     * @return View|RedirectResponse
     */
    public function create(): View|RedirectResponse
    {
        if (Auth::user()->parking)
        {
            return redirect()->route('parking.edit');
        }

        return view('user.parking.create',['days' => Day::orderBy('id')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateParking($request);

        $this->normalizeScheduleTimes($request);
        $this->validateSchedules($request);

        $parking = Parking::create([
            'id_user' => Auth::id(),
            'name' => $data['name'],
            'latitude_coordinate' => $data['lat'],
            'longitude_coordinate' => $data['lng'],
            'type' => (int) $data['type'],
            'price' => (int)$data['type'] === 0 ? ($data['price_flat'] ?? 0) : ($data['price_hour'] ?? 0),
            'price_flat' => $data['price_flat'] ?? null,
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

    public function edit(): View|RedirectResponse
    {
        $parking = Auth::user()->parking;

        if (!$parking)
        {
            return redirect()->route('parking.create');
        }

        return view('user.parking.edit',['parking' => $parking,'days' => Day::orderBy('id')->get()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $parking = Auth::user()->parking;

        if (!$parking)
        {
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
            'price'                => (int)$data['type'] === 0 ? ($data['price_flat'] ?? 0) : ($data['price_hour'] ?? 0),
            'price_flat'           => $data['price_flat'] ?? null,
        ]);

        $this->saveSchedules($parking, $request);

        return back()->with('swal', [
            'icon'  => 'success',
            'title' => '¡Actualizado!',
            'text'  => 'Estacionamiento y horario actualizados correctamente.',
        ]);
    }

    protected function validateParking(Request $request): array
{
    return $request->validate([
        'name' => [
            'required',
            'string',
            'max:30'
        ],
        'lat' => [
            'required',
            'numeric',
            'between:-90,90'
        ],
        'lng' => [
            'required',
            'numeric',
            'between:-180,180'
        ],

        'type'  => [
            'required',
            'integer',
            'in:0,1,2'
        ],

        'price_hour' => [
            'required_if:type,1,2',
            'nullable',
            'numeric',
            'min:0'
        ],
        'price_flat' => 
        [
            'required_if:type,0,2',
            'nullable',
            'numeric',
            'min:0'
        ],
    ], [
        'required' => 'El campo :attribute es obligatorio.',
        'required_if' => 'El campo :attribute es obligatorio para el tipo seleccionado.',
        'numeric' => 'El campo :attribute debe ser numérico.',
        'min' => 'El campo :attribute debe ser mayor o igual a 0.',
        'in'  => 'Tipo de estacionamiento inválido.',
    ], [
        'price_hour' => 'precio por hora',
        'price_flat' => 'precio fijo',
    ]);
}


    protected function saveSchedules(Parking $parking, Request $request): RedirectResponse|null
    {
        $rows = $request->input('schedules', []);
        $sameForAll = $request->boolean('same_schedule');

        if ($sameForAll && isset($rows['all']))
        {
            $globalSchedule = $rows['all'];

            $open = $globalSchedule['open'] ?? null;
            $close = $globalSchedule['close'] ?? null;

            if (!$open || !$close)
            {
                return back()
                    ->withErrors(['schedules.all.open' => 'Debes especificar hora de apertura y cierre.'])
                    ->withInput();
            }

            if ($open >= $close)
            {
                return back()
                    ->withErrors(['schedules.all.close' => 'La hora de cierre debe ser mayor que la de apertura.'])
                    ->withInput();
            }

            unset($rows['all']);

            foreach (Day::orderBy('id')->get() as $day)
            {
                $rows[$day->id] = [
                    'open'   => $open,
                    'close'  => $close,
                    'closed' => false,
                ];
            }
        }

        foreach ($rows as $dayId => $row)
        {
            if (!is_numeric($dayId))
            {
                continue;
            }

            $dayId = (int) $dayId;

            if (!Day::find($dayId))
            {
                continue;
            }

            $closed = !empty($row['closed']);
            $open = $row['open'] ?? null;
            $close = $row['close'] ?? null;

            if ($closed || !$open || !$close)
            {
                Schedule::where('id_parking', $parking->id)
                    ->where('id_day', $dayId)
                    ->delete();

                continue;
            }

            if ($open >= $close)
            {
                if (!$sameForAll)
                {
                    return back()
                        ->withErrors(["schedules.$dayId.close" => 'La hora de cierre debe ser mayor que la de apertura.'])
                        ->withInput();
                }

                continue;
            }

            Schedule::updateOrCreate(['id_parking' => $parking->id,'id_day' => $dayId,],['opening_time' => $open,'closing_time' => $close,]);
        }

        return null;
    }


    protected function normalizeScheduleTimes(Request $request): void
    {
        $schedules = $request->input('schedules', []);

        $norm = function ($t)
        {
            if ($t === null || $t === '')
            {
                return $t;
            }

            $t = trim((string) $t);

            $t = preg_replace('/\s*a\.?\s*m\.?/i', ' AM', $t);
            $t = preg_replace('/\s*p\.?\s*m\.?/i', ' PM', $t);

            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $t))
            {
                return substr($t, 0, 5);
            }

            try
            {
                return Carbon::parse($t)->format('H:i');
            }
            catch (\Throwable $e)
            {
                return $t;
            }
        };

        if (isset($schedules['all']))
        {
            $schedules['all']['open'] = $norm($schedules['all']['open'] ?? null);
            $schedules['all']['close'] = $norm($schedules['all']['close'] ?? null);
        }

        foreach ($schedules as $key => $row)
        {
            if ($key === 'all' || !is_array($row))
            {
                continue;
            }

            $schedules[$key]['open'] = $norm($row['open'] ?? null);
            $schedules[$key]['close'] = $norm($row['close'] ?? null);
        }

        $request->merge([
            'schedules' => $schedules,
        ]);
    }

    protected function validateSchedules(Request $request): void
    {
        $days = Day::orderBy('id')->get();

        $rules = [];

        if ($request->boolean('same_schedule'))
        {
            $rules['schedules.all.open'] = ['required','date_format:H:i',];

            $rules['schedules.all.close'] = [
                'required',
                'date_format:H:i',
                'after:schedules.all.open',
            ];
        }
        else
        {
            foreach ($days as $day)
            {
                $prefix = "schedules.{$day->id}";

                $rules["{$prefix}.closed"] = ['nullable','in:0,1',];

                $rules["{$prefix}.open"] = [
                    'exclude_if:' . $prefix . '.closed,1',
                    'required',
                    'date_format:H:i',
                ];

                $rules["{$prefix}.close"] = [
                    'exclude_if:' . $prefix . '.closed,1',
                    'required',
                    'date_format:H:i',
                    "after:{$prefix}.open",
                ];
            }
        }

        $messages = [
            'required'     => 'El campo :attribute es obligatorio.',
            'date_format'  => 'El campo :attribute debe tener el formato HH:MM.',
            'after'        => 'La hora de cierre debe ser mayor que la de apertura.',
            'in'           => 'Valor inválido.',
        ];

        $attributes = ['schedules.all.open'  => 'apertura (todos los días)','schedules.all.close' => 'cierre (todos los días)',];

        foreach ($days as $day)
        {
            $attributes["schedules.{$day->id}.open"]  = "apertura ({$day->name})";
            $attributes["schedules.{$day->id}.close"] = "cierre ({$day->name})";
        }

        $request->validate(
            $rules,
            $messages,
            $attributes
        );
    }
}
