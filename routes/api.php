<?php
/*
* Nombre de la clase         : api.php
* Descripción de la clase    : Archivo de rutas de la API que define los endpoints y su lógica.
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
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PlanApiController;
use App\Http\Controllers\Api\PayPalApiController;
use App\Http\Controllers\Api\BalanceApiController;
use App\Http\Controllers\Api\EntryApiController;
use App\Http\Controllers\Api\FirebaseApiController;
use App\Http\Controllers\Api\ParkingApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\RegisterProviderApiController;
use App\Http\Controllers\Api\ParkingInboxApiController;
use App\Http\Controllers\Api\UserApprovedTypesApiController;
use App\Http\Controllers\Api\UserParkingRequestApiController;
use App\Http\Controllers\Api\PasswordResetApiController;
 
Route::prefix('auth')->group(function () 
{
    Route::post('/register', [AuthApiController::class, 'register'])->middleware('throttle:6,1');
    Route::post('/login', [AuthApiController::class, 'login'])->middleware('throttle:10,1');
    Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/me', [AuthApiController::class, 'me'])->middleware('auth:sanctum');
 
    Route::post('/register-provider', [RegisterProviderApiController::class, 'registerProvider']);
});
 
Route::get('/plans', [PlanApiController::class, 'index']);
Route::get('/plans/{plan}', [PlanApiController::class, 'show']);
 
Route::middleware('auth:sanctum')->post('/paypal/success', [PayPalApiController::class, 'store']);
 
Route::middleware('auth:sanctum')->group(function () 
{
    Route::get('/user/balance', [BalanceApiController::class, 'index']);
    Route::post('/user/recharge', [BalanceApiController::class, 'store']);
});
 
 
Route::middleware('auth:sanctum')->get('/user/transactions', [PaymentApiController::class, 'history']);
 
Route::middleware('auth:sanctum')->get('/parkings/nearby', [ParkingApiController::class, 'nearby']);
 
Route::prefix('parkings')->group(function () 
{
    Route::get('/with-dynamic-clients', [ParkingApiController::class, 'withDynamicClients']);
    Route::get('/{id}/client-types', [ParkingApiController::class, 'clientTypesByParking']);
});
 
Route::middleware('auth:sanctum')->put('/user/notification-token', [FirebaseApiController::class, 'updateNotificationToken']);
 
Route::middleware('auth:sanctum')->post('/firebase-notification/send/notification', [FirebaseApiController::class, 'send']);
 
Route::middleware('auth:sanctum')->post('/entries/confirm', [EntryApiController::class, 'confirmEntry']);
 
Route::middleware('auth:sanctum')->group(function () 
{
    Route::get('/user/dynamic-parkings',[UserParkingRequestApiController::class, 'index']);
    Route::get('/user/dynamic-parkings', [ParkingInboxApiController::class, 'getInboxParkings']);
    Route::post('/user/parkings/{parkingId}/request', [ParkingInboxApiController::class, 'sendRequest']);
});
 
Route::middleware('auth:sanctum')->get('/user/approved-types', [UserApprovedTypesApiController::class, 'index']);

Route::post('/password/request-code', [PasswordResetApiController::class, 'requestCode']);
Route::post('/password/verify-code', [PasswordResetApiController::class, 'verifyCode']);
Route::post('/password/reset-with-code', [PasswordResetApiController::class, 'resetWithCode']);
