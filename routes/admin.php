<?php
/*
* Nombre de la clase         : admin.php
* Descripción de la clase    : Archivo de rutas para la sección de administración de la aplicación.
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

use App\Http\Controllers\AdminSubscriptionController;
use App\Http\Controllers\AdminUserStatusController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () 
{

    Route::get('/', DashboardController::class)->name('dashboard');

    //Listado de planes
    Route::get('plans', [PlanController::class, 'index'])->name('plans.index');
    //Editar plan
    Route::get('plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
    //Actualizar plan
    Route::put('plans/{plan}', [PlanController::class, 'update'])->name('plans.update');

    Route::patch('users/{user}/toggle-active', AdminUserStatusController::class)
        ->middleware(['auth'])
        ->name('users.toggle-active');

    Route::get('subscriptions', [AdminSubscriptionController::class, 'index'])
        ->middleware(['auth'])
        ->name('subscriptions.index');

    Route::patch('subscriptions/{user}/cancel', [AdminSubscriptionController::class, 'cancel'])
        ->middleware(['auth'])
        ->name('subscriptions.cancel');

    Route::patch('subscriptions/{user}/renew', [AdminSubscriptionController::class, 'renew'])
        ->middleware(['auth'])
        ->name('subscriptions.renew');
});