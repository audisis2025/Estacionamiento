<?php
/*
* Nombre de la clase         : EnsureParkingConfiguredMiddleware.php
* Descripción de la clase    : Asegura que el usuario tenga un estacionamiento configurado y con horario antes de acceder a ciertas rutas.
* Fecha de creación          : 06/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 06/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\RedirectResponse;

class EnsureParkingConfiguredMiddleware
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
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
                    'confirmButtonColor' => '#494949'
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
                    'confirmButtonColor' => '#494949'
                ]);
        }
        return $next($request);
    }
}
