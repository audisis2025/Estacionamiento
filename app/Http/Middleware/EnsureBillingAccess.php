<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBillingAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $u = $request->user();
        if (!$u || (int)($u->id_role ?? 0) !== 2 || (int)($u->id_plan ?? 0) !== 3) {
            abort(403, 'No autorizado.');
        }
        return $next($request);
    }
}
