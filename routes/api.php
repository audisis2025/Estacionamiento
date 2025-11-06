<?php

use App\Http\Controllers\Api\AuthApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlanApiController;
use App\Http\Controllers\Api\PayPalApiController;
use App\Http\Controllers\Api\BalanceApiController;


Route::prefix('auth')->group(
    function () {
        Route::post('/register', [AuthApiController::class, 'register'])->middleware('throttle:6,1');
        Route::post('/login',    [AuthApiController::class, 'login'])->middleware('throttle:10,1');
        Route::post('/logout',   [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
        Route::get('/me',        [AuthApiController::class, 'me'])->middleware('auth:sanctum');
    }
);


Route::get('/plans', [PlanApiController::class, 'index']);
Route::get('/plans/{plan}', [PlanApiController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/paypal/success', [PayPalApiController::class, 'store']);
});




Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/balance', [BalanceApiController::class, 'index']);
    Route::post('/user/recharge', [BalanceApiController::class, 'store']);
});
