<?php
/*
* Nombre de la clase         : web.php
* Descripción de la clase    : Archivo de rutas web de Laravel que define las rutas principales de la aplicación, 
                               incluyendo la página de inicio, configuración del usuario 
                               y términos de servicio.
* Fecha de creación          : 06/10/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 06/10/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Models\Plan;

Route::get('/', function ()
{
    $plans = Plan::where('type', 'parking')
        ->orderBy('price')
        ->orderBy('duration_days')
        ->get();

    return view('welcome', compact('plans'));
})->name('home');

Route::middleware(['auth'])->group(function () 
{
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(when(
                Features::canManageTwoFactorAuthentication() && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::view('/terms', 'terms')->name('terms');

require __DIR__.'/auth.php';
require __DIR__.'/user.php';