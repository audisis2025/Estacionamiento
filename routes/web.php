<?php

use App\Http\Controllers\ParkingController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\PlanPurchaseController;
use App\Http\Controllers\QrReaderController;
use App\Http\Controllers\ScheduleController;
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

Route::view('app', 'dashboard')
    ->middleware(['auth', 'verified', 'ensure.active.plan'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () 
{
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

Route::middleware(['auth'])->group(function () 
{
    Route::get('/plans/choose', [PlanPurchaseController::class, 'choose'])
        ->name('plans.choose');

    Route::post('/plans/pay/{plan}', [PlanPurchaseController::class, 'pay'])
        ->name('plans.pay');

    Route::middleware(['auth'])->group(function () 
    {
        Route::post('/paypal/order/create', [PayPalController::class, 'create'])->name('paypal.order.create');
        Route::post('/paypal/order/{orderId}/capture', [PayPalController::class, 'capture'])->name('paypal.order.capture');
    });
});

Route::middleware(['auth'])->prefix('parking')->name('parking.')->group(function () 
{
    Route::get('create', [ParkingController::class, 'create'])->name('create');
    Route::post('/', [ParkingController::class, 'store'])->name('store');
    Route::get('edit', [ParkingController::class, 'edit'])->name('edit');
    Route::put('/', [ParkingController::class, 'update'])->name('update');
});

Route::middleware(['auth', 'ensure.active.plan', 'ensure.parking.configured'])
    ->prefix('parking/qr-readers')
    ->name('parking.qr-readers.')
    ->group(function () 
    {
        Route::get('/',            [QrReaderController::class, 'index'])->name('index');
        Route::get('/create',      [QrReaderController::class, 'create'])->name('create');
        Route::post('/',           [QrReaderController::class, 'store'])->name('store');
        Route::get('/{reader}/edit',[QrReaderController::class, 'edit'])->name('edit');
        Route::put('/{reader}',    [QrReaderController::class, 'update'])->name('update');
        Route::delete('/{reader}', [QrReaderController::class, 'destroy'])->name('destroy');
    });

require __DIR__.'/auth.php';