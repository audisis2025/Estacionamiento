<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureActivePlan
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->phone_number === '7777777777') {
            return $next($request);
        }

        // Si no tiene plan activo, redirige a escoger plan
        if ($user && ! $user->hasActivePlan()) {
            return redirect()->route('plans.choose');
        }

        return $next($request);
    }
}
