<?php
/*
* Nombre de la clase         : FortifyServiceProvider.php
* Descripción de la clase    : Arranca cualquier servicio de aplicación
* Fecha de creación          : 04/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 03/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/
namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        Fortify::twoFactorChallengeView(fn () => view('livewire.auth.two-factor-challenge'));
        Fortify::confirmPasswordView(fn () => view('livewire.auth.confirm-password'));

        RateLimiter::for('two-factor', function (Request $request) 
        {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
