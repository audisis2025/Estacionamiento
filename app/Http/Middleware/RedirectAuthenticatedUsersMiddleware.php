<?php
/*
* Nombre de la clase         : RedirectAuthenticatedUsersMiddleware.php
* Descripción de la clase    : Middleware para redirigir usuarios autenticados 
                               según su rol (administrador o usuario estándar).
* Fecha de creación          : 08/12/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 08/12/2025
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

class RedirectAuthenticatedUsersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) 
        {
            $user = auth()->user();
            
            if ($user->isAdmin() && $request->is('dashboard')) 
            {
                return redirect()->route('admin.dashboard');
            }
            
            if (!$user->isAdmin() && $request->is('admin*')) 
            {
                return redirect()->route('dashboard');
            }
        }

        return $next($request);
    }
}
