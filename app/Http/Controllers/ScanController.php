<?php
/*
* Nombre de la clase         : ScanController.php
* Descripción de la clase    : Controlador que maneja la lectura e ingestión de códigos QR para registrar 
*                              entradas y salidas de usuarios en un estacionamiento.
* Fecha de creación          : 06/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 06/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0
*/

namespace App\Http\Controllers;

use App\Models\QrReader;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ScanController extends Controller
{

    public function form(QrReader $reader): View
    {
        $this->ensureOwnership($reader);

        return view('user.qr_readers.scan', compact('reader'));
    }

    public function ingest(Request $request, QrReader $reader): JsonResponse
    {
        $this->ensureOwnership($reader);

        $data = $request->validate(['qr' => ['required','string',],]);

        $raw = trim($data['qr']);
        $raw = preg_replace(
            '/^\^?(IN|OUT|MIX)\^?/i',
            '',
            $raw
        );

        $payload = json_decode($raw, true);

        if (!is_array($payload) || !isset($payload['id'], $payload['fechaHora']))
        {
            return $this->fail('QR inválido.');
        }

        try
        {
            $qrTime = Carbon::parse($payload['fechaHora']);
        }catch (\Throwable)
        {
            return $this->fail('Fecha/hora inválida en el QR.');
        }

        if ($qrTime->diffInSeconds(now()) > 15)
        {
            return $this->fail('QR expirado. Vuelve a generar el código (15s).');
        }

        $user = User::find($payload['id']);

        if (!$user)
        {
            return $this->fail('Usuario no encontrado.');
        }

        $parking = auth()->user()->parking;
        $readerIds = $parking->qrReaders()->pluck('id');

        $openTx = Transaction::where('id_user', $user->id)
            ->whereNull('departure_date')
            ->whereIn('id_qr_reader', $readerIds)
            ->latest('id')
            ->first();

        if ($reader->sense === 1 && !$openTx)
        {
            return $this->fail('Este lector es de salida y el usuario no tiene entrada abierta.');
        }

        if ($reader->sense === 0 && $openTx)
        {
            return $this->fail('Este lector es de entrada y el usuario ya tiene una estancia abierta.');
        }

        if (!$openTx)
        {
            $recentEntry = Transaction::where('id_user', $user->id)
                ->whereNull('departure_date')
                ->whereIn('id_qr_reader', $readerIds)
                ->where('entry_date', '>=', now()->subSeconds(3))
                ->exists();

            if ($recentEntry)
            {
                return $this->silent('recent_entry');
            }

            $lastTx = Transaction::where('id_user', $user->id)
                ->whereIn('id_qr_reader', $readerIds)
                ->latest('id')
                ->first();

            if ($lastTx&& $lastTx->departure_date&& $lastTx->departure_date->diffInSeconds(now()) < 5)
            {
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

        if ($openTx->entry_date->diffInSeconds(now()) < 5)
        {
            return $this->silent('too_soon_after_entry');
        }

        $minutes = max(1, $openTx->entry_date->diffInMinutes(now()));

        $charge = $this->computeCharge(
            $user,
            $parking,
            $minutes
        );

        if (!$user->hasEnoughBalance($charge))
        {
            return $this->fail('Saldo insuficiente para completar el pago.');
        }

        try
        {
            DB::transaction(
                function () use ($user, $charge, $openTx, $reader)
                {
                    $affected = User::whereKey($user->id)
                        ->where('amount', '>=', $charge)
                        ->decrement('amount', $charge);

                    if ($affected !== 1)
                    {
                        throw new \RuntimeException('NO_FUNDS');
                    }

                    $openTx->update([
                        'amount'         => (int) round($charge),
                        'departure_date' => now(),
                        'id_qr_reader'   => $reader->id,
                    ]);
                }
            );
        } catch (\RuntimeException $exception)
        {
            if ($exception->getMessage() === 'NO_FUNDS')
            {
                return $this->fail('Saldo insuficiente para completar el pago.');
            }

            throw $exception;
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
                abort(response()->json(['ok' => false,'message' => 'No autorizado.',], 403));
            }

            abort(403, 'No autorizado.');
        }
    }

    private function computeCharge(User $user, $parking, int $minutes): int
    {
        $base = max(0, (int) ($parking->price ?? 0));

        $minutes = max(1, $minutes);

        $raw = ((int) $parking->type === 0) ? $base : $base * (int) ceil($minutes / 60);

        $uct = $user->activeUserClientTypeForParking((int) $parking->id);

        if (!$uct || !$uct->clientType)
        {
            return (int) $raw;
        }

        $ct = $uct->clientType;
        $discounted = $raw;

        if ((int) $ct->discount_type === 0)
        {
            $pct = min(100.0, max(0.0, (float) $ct->amount));

            $discounted = $raw - round( $raw * ($pct / 100), 2);
        } elseif ((int) $ct->discount_type === 1)
        {
            $fixed = max( 0.0, (float) $ct->amount);

            $discounted = $raw - $fixed;
        }

        return (int) max(0, $discounted);
    }

    private function ok(string $msg, array $data = []): JsonResponse
    {
        return response()->json([
            'ok'      => true,
            'message' => $msg,
            'data'    => $data,
        ]);
    }

    private function silent(string $reason): JsonResponse
    {
        return response()->json([
            'ok'     => true,
            'silent' => true,
            'reason' => $reason,
        ]);
    }
    private function fail(string $msg, int $code = 422): JsonResponse
    {
        return response()->json(['ok' => false, 'message' => $msg,], $code);
    }
}