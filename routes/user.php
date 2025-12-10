<?php
/*
* Nombre de la clase         : user.php
* Descripción de la clase    : Define las rutas web relacionadas con las funcionalidades del usuario,
                               incluyendo la gestión de estacionamientos, lectores QR, tipos de clientes, 
                               entradas y facturación.
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
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ClientTypeApprovalController;
use App\Http\Controllers\ClientTypeController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\ParkingController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\PlanPurchaseController;
use App\Http\Controllers\QrReaderController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () 
{
    Route::get('/plans/choose', [PlanPurchaseController::class, 'choose'])->name('plans.choose');

    Route::post('/plans/pay/{plan}', [PlanPurchaseController::class, 'pay'])->name('plans.pay');

    Route::middleware(['auth'])->group(function () 
    {
        Route::post('/paypal/order/create', [PayPalController::class, 'create'])->name('paypal.order.create');
        Route::post('/paypal/order/{orderId}/capture', [PayPalController::class, 'capture'])->name('paypal.order.capture');
    });
});

Route::middleware(['auth','verified','ensure.active.plan'])
    ->group(function () 
    {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [UserDashboardController::class, 'data'])->name('dashboard.data');
    }
);

Route::middleware(['auth','ensure.active.plan'])->prefix('parking')->name('parking.')->group(function () 
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
        Route::get('/', [QrReaderController::class, 'index'])->name('index');
        Route::get('/create', [QrReaderController::class, 'create'])->name('create');
        Route::post('/', [QrReaderController::class, 'store'])->name('store');
        Route::get('/{reader}/edit',[QrReaderController::class, 'edit'])->name('edit');
        Route::put('/{reader}', [QrReaderController::class, 'update'])->name('update');
        Route::delete('/{reader}', [QrReaderController::class, 'destroy'])->name('destroy');
        Route::get('{reader}/scan',  [ScanController::class, 'form'])->name('scan');
        Route::post('{reader}/scan', [ScanController::class, 'ingest'])->name('scan.ingest');
        Route::post('{reader}/simulate', [ScanController::class, 'simulate'])->name('scan.simulate');
    }
);

Route::middleware(['auth', 'ensure.active.plan', 'ensure.parking.configured'])
    ->prefix('parking/client-types')
    ->name('parking.client-types.')
    ->group(function () 
    {
        Route::get('/', [ClientTypeController::class, 'index'])->name('index');
        Route::get('/create', [ClientTypeController::class, 'create'])->name('create');
        Route::post('/', [ClientTypeController::class, 'store'])
            ->name('store')
            ->missing(function () 
            {
                return redirect()->back()->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Ya eliminado',
                    'text'  => 'El tipo de cliente fue eliminado previamente.',
                    'confirmButtonColor' => '#494949'
                ]);
            }
        );
        Route::get('/{clientType}/edit',[ClientTypeController::class, 'edit'])
            ->name('edit')
            ->missing(function () 
            {
                return redirect()->back()->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Ya eliminado',
                    'text'  => 'El tipo de cliente fue eliminado previamente.',
                    'confirmButtonColor' => '#494949'
                ]);
            }
        );
        Route::put('/{clientType}', [ClientTypeController::class, 'update'])
            ->name('update')
            ->missing(function () 
            {
                return redirect()->back()->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Ya eliminado',
                    'text'  => 'El tipo de cliente fue eliminado previamente.',
                    'confirmButtonColor' => '#494949'
                ]);
            }
        );
        Route::delete('/{clientType}', [ClientTypeController::class, 'destroy'])
            ->name('destroy')
            ->missing(function () 
            {
                return redirect()->back()->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Ya eliminado',
                    'text'  => 'El tipo de cliente fue eliminado previamente.',
                    'confirmButtonColor' => '#494949'
                ]);
            }
        );
    }
);

Route::middleware(['auth', 'ensure.active.plan', 'ensure.parking.configured'])
    ->prefix('parking/client-approvals')
    ->name('parking.client-approvals.')
    ->group(function () 
    {
        Route::get('/', [ClientTypeApprovalController::class, 'index'])->name('index');
        Route::post('{userClientType}/approve', [ClientTypeApprovalController::class, 'approve'])
            ->name('approve') 
            ->missing(function () 
            {
                return redirect()->back()->with('swal', [
                    'icon'  => 'error',
                    'title' => 'No encontrado',
                    'text'  => 'La solicitud ya no existe o fue eliminada.',
                    'confirmButtonColor' => '#494949'
                ]);
            }
        );
        Route::delete('{userClientType}', [ClientTypeApprovalController::class, 'reject'])
            ->name('reject')
            ->missing(function () 
            {
                return redirect()->back()->with('swal', [
                    'icon'  => 'error',
                    'title' => 'Ya eliminado',
                    'text'  => 'La solicitud ya fue eliminada previamente.',
                    'confirmButtonColor' => '#494949'
                ]);
            }
        );
    }
);

Route::middleware(['auth','ensure.active.plan','ensure.parking.configured'])
    ->prefix('parking/entries')->name('parking.entries.')
    ->group(function () 
    {
        Route::get('/', [EntryController::class, 'index'])->name('index');
        Route::post('{transaction}/release', [EntryController::class, 'release'])->name('release');
    }
);

Route::middleware(['auth','verified','ensure.active.plan','ensure.parking.configured','ensure.billing.access'])
    ->prefix('billing')->name('billing.')
    ->group(function () 
    {
        Route::get('/', [BillingController::class, 'index'])->name('index');
    }
);
