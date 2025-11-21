<?php
/*
* Nombre de la clase         : EntryController.php
* Descripción de la clase    : Controlador que gestiona los registros de entrada y salida de usuarios en los lectores QR.
* Fecha de creación          : 04/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 05/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0
* Fecha de mantenimiento     :
* Folio de mantenimiento     : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EntryController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $parking = $user->parking;
        $readerIds = $parking->qrReaders()->pluck('id');

        $phone = trim($request->input('q', ''));

        $entries = Transaction::query()
            ->with(['user:id,name,email,phone_number',])
            ->whereIn('id_qr_reader', $readerIds)
            ->where(function ($where)
            {
                $where->whereNull('departure_date')
                    ->orWhere(
                        'departure_date',
                        '=',
                        '0000-00-00 00:00:00'
                    );
            })
            ->when( $phone !== '',
                function ($query) use ($phone)
                {
                    $query->whereHas(
                        'user',
                        function ($sub) use ($phone)
                        {
                            $sub->where(
                                'phone_number',
                                'like',
                                "%{$phone}%"
                            );
                        }
                    );
                }
            )
            ->orderByDesc('entry_date')
            ->paginate(12)
            ->withQueryString();

        return view('user.parking.entries.index', compact('entries', 'phone'));
    }

    public function release(Request $request, Transaction $transaction): RedirectResponse
    {
        $user = Auth::user();
        $parking = $user->parking;

        if ( !$parking || !$parking->qrReaders()->where('id', $transaction->id_qr_reader)->exists())
        {
            return back()->with('error', 'No autorizado para liberar esta transacción.');
        }

        try
        {
            DB::transaction(function () use ($transaction, $parking)
            {
                $t = Transaction::whereKey($transaction->id)
                    ->lockForUpdate()
                    ->first();

                if (!is_null($t->departure_date))
                {
                    abort(409, 'La transacción ya fue liberada o cerrada.');
                }

                $releasedAt = Carbon::now();
                $price = (float) $parking->price;
                $charge = 0;

                if ((int) $parking->type === 1)
                {
                    $hours = max(1, ceil( $t->entry_date->diffInMinutes($releasedAt) / 60));

                    $charge = $hours * $price;
                }
                else
                {
                    $charge = $price;
                }

                $t->amount = $charge;
                $t->departure_date = $releasedAt;
                $t->save();
            });

            return back()->with('ok', 'Salida liberada correctamente. Monto calculado automáticamente.');
        }
        catch (\Throwable $e)
        {
            return back()->with('error', 'Ocurrió un error al liberar la salida.');
        }
    }
}
