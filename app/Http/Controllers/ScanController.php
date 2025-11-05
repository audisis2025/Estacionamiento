<?php

namespace App\Http\Controllers;

use App\Models\QrReader;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function form(QrReader $reader)
    {
        $this->ensureOwnership($reader);
        return view('user.qr_readers.scan', compact('reader'));
    }

    public function ingest(Request $request, QrReader $reader)
    {
        $this->ensureOwnership($reader);

        $data = $request->validate([
            'qr' => ['required', 'string'],
        ]);

        // Limpieza opcional de prefijos
        $raw = trim($data['qr']);
        $raw = preg_replace('/^\^?(IN|OUT|MIX)\^?/i', '', $raw);

        $payload = json_decode($raw, true);
        if (!is_array($payload) || !isset($payload['id'], $payload['fechaHora'])) {
            return $this->fail('QR inválido.');
        }

        // Validez 15s
        try {
            $qrTime = Carbon::parse($payload['fechaHora']);
        } catch (\Throwable $e) {
            return $this->fail('Fecha/hora inválida en el QR.');
        }
        if ($qrTime->diffInSeconds(now()) > 15) {
            return $this->fail('QR expirado. Vuelve a generar el código (15s).');
        }

        /** @var User|null $user */
        $user = User::find($payload['id']);
        if (!$user) return $this->fail('Usuario no encontrado.');

        $parking   = auth()->user()->parking;
        $readerIds = $parking->qrReaders()->pluck('id');

        // Transacción abierta en ESTE estacionamiento
        $openTx = Transaction::where('id_user', $user->id)
            ->whereNull('departure_date')
            ->whereIn('id_qr_reader', $readerIds)
            ->latest('id')
            ->first();

        // Reglas por sentido
        if ($reader->sense === 1 && !$openTx) {
            return $this->fail('Este lector es de salida y el usuario no tiene entrada abierta.');
        }
        if ($reader->sense === 0 && $openTx) {
            return $this->fail('Este lector es de entrada y el usuario ya tiene una estancia abierta.');
        }

        // ===== ENTRADA =====
        if (!$openTx) {
            // Anti doble entrada (3s) -> SILENCIAR
            $recentEntry = Transaction::where('id_user', $user->id)
                ->whereNull('departure_date')
                ->whereIn('id_qr_reader', $readerIds)
                ->where('entry_date', '>=', now()->subSeconds(3))
                ->exists();
            if ($recentEntry) {
                // No mostramos alerta al usuario
                return $this->silent('recent_entry');
            }

            // Anti "salida seguida de nueva entrada" (5s) -> SILENCIAR
            $lastTx = Transaction::where('id_user', $user->id)
                ->whereIn('id_qr_reader', $readerIds)
                ->latest('id')
                ->first();
            if ($lastTx && $lastTx->departure_date && $lastTx->departure_date->diffInSeconds(now()) < 5) {
                return $this->silent('post_exit_bounce');
            }

            $tx = Transaction::create([
                'amount'         => null,
                'entry_date'     => now(),
                'departure_date' => null,
                'id_qr_reader'   => $reader->id,
                'id_user'        => $user->id,
            ]);

            return $this->ok('Entrada registrada', [
                'event'          => 'entry',
                'transaction_id' => $tx->id,
                'when'           => $tx->entry_date->toDateTimeString(),
            ]);
        }

        // ===== SALIDA =====
        // Rebote (estancia <5s) -> SILENCIAR en lugar de error visible
        if ($openTx->entry_date->diffInSeconds(now()) < 5) {
            return $this->silent('too_soon_after_entry');
        }

        $minutes = max(1, $openTx->entry_date->diffInMinutes(now()));
        $charge  = $this->computeCharge($user, $parking, $minutes);

        // Dinámicos (rol != 3) pagan con wallet
        if ($this->paysWithWallet($user)) {
            if ($user->amount < $charge) {
                return $this->fail('Saldo insuficiente para completar el pago.');
            }
            $user->decrement('amount', $charge);
        }

        $openTx->update([
            'amount'         => (int) round($charge),
            'departure_date' => now(),
            'id_qr_reader'   => $reader->id,
        ]);

        return $this->ok('Salida registrada', [
            'event'          => 'exit',
            'transaction_id' => $openTx->id,
            'charged'        => $charge,
            'minutes'        => $minutes,
        ]);
    }

    private function ensureOwnership(QrReader $reader): void
    {
        $parking = auth()->user()->parking;
        abort_unless($parking && $reader->id_parking === $parking->id, 403, 'No autorizado.');
    }

    private function computeCharge($user, $parking, int $minutes): int
    {
        $base = (int) ($parking->price ?? 0);
        $raw  = ((int)$parking->type === 0) ? $base : $base * max(1, (int)ceil($minutes / 60));

        if ((int)($user->id_role ?? 3) === 3) return max(0, $raw);

        $clientTypeRel = $user->clientTypes()
            ->where('approval', 1)
            ->whereDate('expiration_date', '>=', now()->toDateString())
            ->whereHas('clientType', fn($q) => $q->where('id_parking', $parking->id))
            ->latest('id')
            ->first();

        if (!$clientTypeRel || !$clientTypeRel->clientType) return max(0, $raw);

        $ct = $clientTypeRel->clientType; // 0=% 1=$
        if ((int)$ct->discount_type === 0) {
            $discount = round($raw * (floatval($ct->amount) / 100), 2);
            return (int) max(0, $raw - $discount);
        }
        return (int) max(0, $raw - (float) $ct->amount);
    }

    private function paysWithWallet($user): bool
    {
        return (int)($user->id_role ?? 3) !== 3;
    }

    private function ok(string $msg, array $data = [])
    {
        return response()->json(['ok' => true, 'message' => $msg, 'data' => $data]);
    }

    // Respuesta silenciosa (no mostrar modal en el frontend)
    private function silent(string $reason)
    {
        return response()->json(['ok' => true, 'silent' => true, 'reason' => $reason]);
    }

    private function fail(string $msg, int $code = 422)
    {
        return response()->json(['ok' => false, 'message' => $msg], $code);
    }
}
