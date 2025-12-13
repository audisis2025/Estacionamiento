<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PaymentApiController extends Controller
{
    public function history(Request $request)
    {
        $user = Auth::user();

        $transactions = Transaction::with(['qrReader.parking'])
            ->where('id_user', $user->id)
            ->whereNotNull('departure_date')
            ->orderByDesc('entry_date')
            ->get(['id', 'amount', 'entry_date', 'departure_date', 'id_qr_reader', 'id_user']);

        $data = $transactions->map(function ($t) {
            $qrReader = $t->qrReader;
            $parking = $qrReader?->parking;

            return [
                'id' => $t->id,
                'amount' => (float) $t->amount,

                'entry_date' => $t->entry_date
                    ? Carbon::parse($t->entry_date)->format('Y-m-d H:i:s')
                    : null,
                'departure_date' => $t->departure_date
                    ? Carbon::parse($t->departure_date)->format('Y-m-d H:i:s')
                    : null,

                'qr_reader' => [
                    'id' => $qrReader->id ?? null,
                    'serial_number' => $qrReader->serial_number ?? 'Desconocido',
                ],
                'parking' => [
                    'id' => $parking->id ?? null,
                    'name' => $parking->name ?? 'Desconocido',
                    'price' => $parking->price ?? 0,
                    'latitude' => $parking->latitude_coordinate ?? null,
                    'longitude' => $parking->longitude_coordinate ?? null,
                ],
            ];
        });

        return response()->json([
            'success' => true,
            'transactions' => $data,
        ]);
    }
}
