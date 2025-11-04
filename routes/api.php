<?php

use App\Http\Controllers\Api\AuthApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(
    function () 
    {
        Route::post('/register', [AuthApiController::class, 'register'])->middleware('throttle:6,1');
        Route::post('/login',    [AuthApiController::class, 'login'])->middleware('throttle:10,1');
        Route::post('/logout',   [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/me',        [AuthApiController::class, 'me'])->middleware('auth:sanctum');
    }
);
