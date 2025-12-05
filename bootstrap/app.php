<?php
/*
* Nombre de la clase         : app.php
* Descripción de la clase    : Archivo de configuración principal de la aplicación Laravel, 
                               que establece rutas, middleware y manejo de excepciones.
* Fecha de creación          : 
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

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
        $middleware->alias([
            'ensure.active.plan' => \App\Http\Middleware\EnsureActivePlanMiddleware::class,
            'ensure.parking.configured'=> \App\Http\Middleware\EnsureParkingConfiguredMiddleware::class,
            'ensure.billing.access' => \App\Http\Middleware\EnsureBillingAccessMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) 
    {
        //
    })->create();
