<?php
/*
* Nombre de la clase         : api.php
* Descripción de la clase    : Archivo de rutas de la API que define los endpoints y su lógica.
* Fecha de creación          : 24/10/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 24/10/2025
* Autorizó                   : Angel Davila
* Versión                    : 2.0
* Fecha de mantenimiento     : 12/01/2026
* Folio de mantenimiento     : L0033
* Tipo de mantenimiento      : Correctivo
* Descripción del mantenimiento : Agregacion y corrección de las rutas del api
* Responsable                : Jonathan Diaz
* Revisor                    : Angel Davila
*/
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\EntryApiController;
use App\Http\Controllers\Api\PlanApiController;
use App\Http\Controllers\Api\PayPalApiController;
use App\Http\Controllers\Api\FirebaseApiController;
use App\Http\Controllers\Api\ParkingApiController;
use App\Http\Controllers\Api\PaymentApiController;
use App\Http\Controllers\Api\PasswordResetApiController;
use App\Http\Controllers\Api\QrApiController;
use App\Http\Controllers\Api\UserDynamicInboxApiController;

Route::middleware(['auth:sanctum'])->group(function ()
{
   
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/me', [AuthApiController::class, 'me']);
 
    Route::post('/qr/generate',[QrApiController::class, 'generateQr']);
 
    Route::get('/user/transactions', [PaymentApiController::class, 'history']);
 
    Route::get('/parkings/nearby', [ParkingApiController::class, 'nearby']);
 
    Route::put('/user/notification-token', [FirebaseApiController::class, 'updateNotificationToken']);
    Route::post('/firebase-notification/send/notification', [FirebaseApiController::class, 'send']);
 
    Route::post('/paypal/create', [PayPalApiController::class, 'create']);
    Route::post('/paypal/capture/{orderId}', [PayPalApiController::class, 'capture']);
    Route::post('/entries/confirm', [EntryApiController::class, 'confirmEntry']);

    Route::post('parkings/{parkingId}/dynamic-inbox/request', [UserDynamicInboxApiController::class, 'sendRequest']);
    Route::get('dynamic-inbox/pending', [UserDynamicInboxApiController::class, 'userPending']);
    Route::get('dynamic-inbox/approved', [UserDynamicInboxApiController::class, 'userApproved']);
});
 
Route::post('/register', [AuthApiController::class, 'register'])->middleware('throttle:6,1');
Route::post('/dynamic-register',[AuthApiController::class, 'dynamicRegister'])->middleware('throttle:6,1');
Route::post('/login', [AuthApiController::class, 'login'])->middleware('throttle:10,1');
 
Route::get('/plans', [PlanApiController::class, 'index']);
Route::get('/plans/{plan}', [PlanApiController::class, 'show']);
 
Route::post('/password/request-code', [PasswordResetApiController::class, 'requestCode']);
Route::post('/password/reset-with-code', [PasswordResetApiController::class, 'resetWithCode']);