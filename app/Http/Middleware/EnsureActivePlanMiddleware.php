<?php
/*
* Nombre de la clase         : EnsureActivePlanMiddleware.php
* Descripción de la clase    : Protege las rutas para los planes, asegurando que el usuario tenga un plan activo o sea administrador.
* Fecha de creación          : 06/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 06/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureActivePlanMiddleware
{
    public function handle(Request $request, Closure $next):mixed
    {
        $user = $request->user();

        if ($user && $user->isAdmin()) 
        {
            return $next($request);
        }

        if ($user && ! $user->hasActivePlan()) 
        {
            return redirect()->route('plans.choose');
        }

        return $next($request);
    }
}
