<?php

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
