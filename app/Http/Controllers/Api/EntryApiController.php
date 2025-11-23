<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QrReader;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EntryApiController extends Controller
{
    public function confirmEntry(Request $request): JsonResponse
    {
        $user = $request->user(); // auth:sanctum

        $data = $request->validate([
            'qr_reader_id' => ['required', 'integer', 'exists:qr_readers,id'],
            'billing_mode' => ['required', 'in:hour,flat'],
            'qr_timestamp' => ['nullable', 'date'],
        ]);

        $reader  = QrReader::with('parking')->findOrFail($data['qr_reader_id']);
        $parking = $reader->parking;

        if (! $parking || (int) $parking->type !== 2) {
            return response()->json([
                'message' => 'El estacionamiento no es de tipo mixto.',
            ], 422);
        }

        // Evitar que el usuario tenga ya una entrada abierta en ese parking
        $hasOpen = Transaction::where('id_user', $user->id)
            ->whereNull('departure_date')
            ->where('id_qr_reader', $reader->id)
            ->exists();

        if ($hasOpen) {
            return response()->json([
                'message' => 'Ya tienes una entrada abierta en este estacionamiento.',
            ], 409);
        }

        $entryDate = isset($data['qr_timestamp'])
            ? Carbon::parse($data['qr_timestamp'])
            : now();

        $tx = Transaction::create([
            'amount'         => null,
            'entry_date'     => $entryDate,
            'departure_date' => null,
            'id_qr_reader'   => $reader->id,
            'id_user'        => $user->id,
            'billing_mode'   => $data['billing_mode'],
        ]);

        return response()->json([
            'message' => 'entry_confirmed',
            'transaction' => [
                'id'          => $tx->id,
                'entry_date'  => $tx->entry_date->toDateTimeString(),
                'billing_mode'=> $tx->billing_mode,
            ],
        ], 201);
    }
}
