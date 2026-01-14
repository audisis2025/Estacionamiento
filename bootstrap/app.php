<?php
/*
* Nombre de la clase         : app.php
* Descripción de la clase    : Archivo de configuración principal de la aplicación Laravel, 
                               que establece rutas, middleware y manejo de excepciones.
* Fecha de creación          : 06/10/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 06/10/2025
* Autorizó                   : Angel Davila
* Versión                    : 2.0 
* Fecha de mantenimiento     : 08/12/2025
* Folio de mantenimiento     : L0015
* Tipo de mantenimiento      : Perfectivo
* Descripción del mantenimiento : Agregado middleware para redirección automática según rol
* Responsable                : Elian Pérez
* Revisor                    : Angel Davila
*/

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',
        then: function()
        {
            Route::middleware('web', 'auth')
                ->group(base_path('routes/admin.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware)
    {
        $middleware->web(append: [ \App\Http\Middleware\RedirectAuthenticatedUsersMiddleware::class]);
       
        $middleware->alias([
            'ensure.active.plan' => \App\Http\Middleware\EnsureActivePlanMiddleware::class,
            'ensure.parking.configured'=> \App\Http\Middleware\EnsureParkingConfiguredMiddleware::class,
            'ensure.billing.access' => \App\Http\Middleware\EnsureBillingAccessMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions)
    {
        $exceptions->render(function (Throwable $e, $request)
        {
            if ($request->is('api/*'))
            {
                if ($e instanceof ValidationException)
                {
                    $errorList = collect($e->errors())->flatten()->all();
                    return response()->json(['message' => $errorList, "statusCode" => 422], 422);
                }
                if ($e instanceof AuthenticationException)
                {
                    return response()->json(['message' => 'Token inválido o no proporcionado', 'statusCode' => 401], 401);
                }
                if ($e instanceof HttpExceptionInterface)
                {
                    return response()->json(['message' => $e->getMessage(), "statusCode" => $e->getStatusCode()], $e->getStatusCode());
                }
                return response()->json(['message' => $e->getMessage(), "statusCode" => 500], 500);
            }
            return null;
        });
    })->create();
 
