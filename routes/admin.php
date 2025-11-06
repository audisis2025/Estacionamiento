<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PlanController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('plans', [PlanController::class, 'index'])->name('plans.index');

    //Listado de planes
    Route::get('plans', [PlanController::class, 'index'])->name('plans.index');
    //Editar plan
    Route::get('plans/{plan}/edit', [PlanController::class, 'edit'])->name('plans.edit');
    //Actualizar plan
    Route::put('plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
});