<?php

namespace App\Http\Controllers;

use App\Models\QrReader;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        if ($openTx->entry_date->diffInSeconds(now()) < 5) {
            return $this->silent('too_soon_after_entry');
        }

        $minutes = max(1, $openTx->entry_date->diffInMinutes(now()));
        $charge  = $this->computeCharge($user, $parking, $minutes);

        // 1) Validar saldo suficiente
        if (!$user->hasEnoughBalance($charge)) {
            return $this->fail('Saldo insuficiente para completar el pago.');
        }
        // 2) Cobrar y cerrar transacción de forma atómica
        try {
            DB::transaction(function () use ($user, $charge, $openTx, $reader) {
                // Evitar carreras: solo descuenta si alcanza el saldo
                $affected = User::whereKey($user->id)
                    ->where('amount', '>=', $charge)
                    ->decrement('amount', $charge);

                if ($affected !== 1) {
                    // Marca de control interna
                    throw new \RuntimeException('NO_FUNDS');
                }
                $openTx->update([
                    'amount'         => (int) round($charge),
                    'departure_date' => now(),
                    'id_qr_reader'   => $reader->id,
                ]);
            });
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'NO_FUNDS') {
                // Mensaje visible en SweetAlert (res.ok === false)
                return $this->fail('Saldo insuficiente para completar el pago.');
            }
            throw $e; // otros errores siguen siendo 500
        }
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
        $authorized = $parking && $reader->id_parking === $parking->id;

        if (!$authorized) 
        {
            if (request()->expectsJson()) 
            {
                abort(response()->json(['ok' => false, 'message' => 'No autorizado.'], 403));
            }
            abort(403, 'No autorizado.');
        }
    }


    private function computeCharge(User $user, $parking, int $minutes): int
    {
        $base = max(0, (int) ($parking->price ?? 0));         // nunca negativo
        $minutes = max(1, $minutes);                          // por si llega 0/negativo

        // type 0 = tarifa fija por estancia, !=0 = por hora (redondeo hacia arriba, mínimo 1h)
        $raw  = ((int)$parking->type === 0)
            ? $base
            : $base * (int) ceil($minutes / 60);

        // Sin relación aprobada+vigente para este parking => cobra normal
        $uct = $user->activeUserClientTypeForParking((int)$parking->id);
        if (!$uct || !$uct->clientType) {
            return (int) $raw; // ya es ≥ 0
        }

        $ct = $uct->clientType; // 0=%  |  1=$
        $discounted = $raw;

        if ((int)$ct->discount_type === 0) {
            // Porcentaje (cap opcional a 100 para evitar “descuentos” >100%)
            $pct = min(100.0, max(0.0, (float) $ct->amount));
            $discounted = $raw - round($raw * ($pct / 100), 2);
        } elseif ((int)$ct->discount_type === 1) {
            // Cantidad fija
            $fixed = max(0.0, (float) $ct->amount);
            $discounted = $raw - $fixed;
        } else {
            // Tipo de descuento inválido => cobra normal
            $discounted = $raw;
        }

        // Nunca negativo y regresa entero (tu columna es int)
        return (int) max(0, $discounted);
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
