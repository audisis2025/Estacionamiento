<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EntryController extends Controller
{
    public function index(Request $request)
    {
        $user      = Auth::user();
        $parking   = $user->parking;
        $readerIds = $parking->qrReaders()->pluck('id');

        // Filtro por número de teléfono
        $phone = trim($request->input('q', ''));

        $entries = Transaction::query()
            ->with(['user:id,name,email,phone_number']) // incluye teléfono
            ->whereIn('id_qr_reader', $readerIds) // solo del estacionamiento actual
            ->where(function ($w) {
                $w->whereNull('departure_date')
                    ->orWhere('departure_date', '=', '0000-00-00 00:00:00');
            })
            ->when($phone !== '', function ($query) use ($phone) {
                // Buscar por teléfono exacto o parcial
                $query->whereHas('user', function ($u) use ($phone) {
                    $u->where('phone_number', 'like', "%$phone%");
                });
            })
            ->orderByDesc('entry_date')
            ->paginate(12)
            ->withQueryString();

        return view('user.parking.entries.index', compact('entries', 'phone'));
    }

    public function release(Request $request, Transaction $transaction)
    {
        $user    = Auth::user();
        $parking = $user->parking;

        if (!$parking || !$parking->qrReaders()->where('id', $transaction->id_qr_reader)->exists()) {
            return back()->with('error', 'No autorizado para liberar esta transacción.');
        }

        try {
            DB::transaction(function () use ($transaction, $parking) {
                $t = Transaction::whereKey($transaction->id)->lockForUpdate()->first();

                if (!is_null($t->departure_date)) {
                    abort(409, 'La transacción ya fue liberada o cerrada.');
                }

                $releasedAt = Carbon::now();
                $price = (float) $parking->price;
                $charge = 0;

                if ((int) $parking->type === 1) {
                    // Por hora
                    $hours = max(1, ceil($t->entry_date->diffInMinutes($releasedAt) / 60));
                    $charge = $hours * $price;
                } else {
                    // Tarifa fija
                    $charge = $price;
                }

                $t->amount = $charge;
                $t->departure_date = $releasedAt;
                $t->save();
            });

            return back()->with('ok', 'Salida liberada correctamente. Monto calculado automáticamente.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Ocurrió un error al liberar la salida.');
        }
    }
}
