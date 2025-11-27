<?php
/*
* Nombre de la clase         : EnsureBillingAccess.php
* Descripción de la clase    : Protege las rutas de facturación, limitando el acceso solo a usuarios con rol y plan específico.
* Fecha de creación          : 05/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 05/11/2025
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

class EnsureBillingAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $u = $request->user();
        if (!$u || (int)($u->id_role ?? 0) !== 2 || (int)($u->id_plan ?? 0) !== 3) 
        {
            abort(403, 'No autorizado.');
        }
        return $next($request);
    }
}
