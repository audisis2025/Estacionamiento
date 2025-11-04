<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureParkingConfigured
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $parking = $user?->parking;

        // 1) Si NO hay parking -> ve directo a CREATE (no a edit)
        if (!$parking) {
            return redirect()
                ->route('parking.create')
                ->with('swal', [
                    'icon'  => 'warning',
                    'title' => 'Configura tu estacionamiento',
                    'text'  => 'Debes dar de alta tu estacionamiento antes de registrar lectores.',
                ]);
        }

        // 2) Si hay parking pero SIN horarios -> ve a EDIT (ahí configuras horarios)
        $hasSchedule = $parking->schedules()->exists();
        if (!$hasSchedule) {
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
