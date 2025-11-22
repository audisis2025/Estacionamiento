<?php

use App\Http\Controllers\Api\AuthApiController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PlanApiController;
use App\Http\Controllers\Api\PayPalApiController;
use App\Http\Controllers\Api\BalanceApiController;
use App\Http\Controllers\Api\FirebaseApiController;
use App\Http\Controllers\Api\ParkingApiController;
use App\Http\Controllers\Api\ParkingClientApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\RegisterProviderApiController;
use App\Http\Controllers\UserDashboardController;

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

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/transactions', [PaymentApiController::class, 'history']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/parkings/nearby', [ParkingApiController::class, 'nearby']);
});

Route::prefix('parkings')->group(function () {
    Route::get('/with-dynamic-clients', [ParkingApiController::class, 'withDynamicClients']);
    Route::get('/{id}/client-types', [ParkingApiController::class, 'clientTypesByParking']);
});

Route::post('/auth/register-provider', [RegisterProviderApiController::class, 'registerProvider']);

//cambiar controlador
// Route::put('/users/notification_token/{id}', [UserDashboardController::class, 'updateNotificationToken']);

Route::middleware('auth:sanctum')->put(
    '/user/notification-token',
    [UserDashboardController::class, 'updateNotificationToken']
);

Route::middleware('auth:sanctum')->group(function()
{
    Route::post('/firebase-notification/send/notification', [FirebaseApiController::class, 'send']);
}
);