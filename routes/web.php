<?php

use App\Http\Controllers\BillingController;
use App\Http\Controllers\ClientTypeApprovalController;
use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\ParkingController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\PlanPurchaseController;
use App\Http\Controllers\QrReaderController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserDashboardController;
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

Route::middleware(['auth','verified','ensure.active.plan'])
    ->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [UserDashboardController::class, 'data'])->name('dashboard.data');
    });

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
        Route::get('{reader}/scan',  [ScanController::class, 'form'])->name('scan');
        Route::post('{reader}/scan', [ScanController::class, 'ingest'])->name('scan.ingest');
    });

Route::middleware(['auth', 'ensure.active.plan', 'ensure.parking.configured'])
    ->prefix('parking/client-types')
    ->name('parking.client-types.')
    ->group(function () 
    {
        Route::get('/',                 [ClientTypeController::class, 'index'])->name('index');
        Route::get('/create',           [ClientTypeController::class, 'create'])->name('create');
        Route::post('/',                [ClientTypeController::class, 'store'])->name('store');
        Route::get('/{clientType}/edit',[ClientTypeController::class, 'edit'])->name('edit');
        Route::put('/{clientType}',     [ClientTypeController::class, 'update'])->name('update');
        Route::delete('/{clientType}',  [ClientTypeController::class, 'destroy'])->name('destroy');
    });

Route::middleware(['auth', 'ensure.active.plan', 'ensure.parking.configured'])
    ->prefix('parking/client-approvals')
    ->name('parking.client-approvals.')
    ->group(function () 
    {
        Route::get('/',                                 [ClientTypeApprovalController::class, 'index'])->name('index');
        Route::post('{userClientType}/approve',         [ClientTypeApprovalController::class, 'approve'])->name('approve');
        Route::delete('{userClientType}',               [ClientTypeApprovalController::class, 'reject'])->name('reject');
    });

Route::middleware(['auth','ensure.active.plan','ensure.parking.configured'])
    ->prefix('parking/entries')->name('parking.entries.')
    ->group(function () 
    {
        Route::get('/', [EntryController::class, 'index'])->name('index');
        Route::post('{transaction}/release', [EntryController::class, 'release'])->name('release');
    });

Route::middleware(['auth','verified','ensure.active.plan','ensure.parking.configured','ensure.billing.access'])
    ->prefix('billing')->name('billing.')
    ->group(function () 
    {
        Route::get('/', [BillingController::class, 'index'])->name('index');
    });

Route::view('/terms', 'terms')->name('terms');

require __DIR__.'/auth.php';