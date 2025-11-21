<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureParkingConfigured
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $parking = $user?->parking;

        if (!$parking)
        {
            return redirect()
                ->route('parking.create')
                ->with('swal', [
                    'icon'  => 'warning',
                    'title' => 'Configura tu estacionamiento',
                    'text'  => 'Debes dar de alta tu estacionamiento antes de registrar lectores.',
                ]);
        }

        $hasSchedule = $parking->schedules()->exists();
        if (!$hasSchedule) 
        {
            return redirect()
                ->route('parking.edit')
                ->with('swal', [
                    'icon'  => 'warning',
                    'title' => 'Falta tu horario',
                    'text'  => 'Configura el horario del estacionamiento antes de registrar lectores.',
                ]);
        }

        return $next($request);
    }
}
