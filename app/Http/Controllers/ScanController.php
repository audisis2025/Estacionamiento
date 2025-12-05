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
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/

namespace App\Http\Controllers;

use App\Models\QrReader;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ScanController extends Controller
{
    public function __construct(private FirebaseService $firebase)
    {
    }

    public function form(QrReader $reader): View
    {
        $this->ensureOwnership($reader);

        return view('user.qr_readers.scan', compact('reader'));
    }

    public function ingest(Request $request, QrReader $reader): JsonResponse
    {
        $this->ensureOwnership($reader);

        $data = $request->validate(['qr' => ['required', 'string']]);

        $raw = trim($data['qr']);
        $raw = preg_replace(
            '/^\^?(IN|OUT|MIX)\^?/i',
            '',
            $raw
        );

        $payload = json_decode($raw, true);

        if (! is_array($payload) || ! isset($payload['id'], $payload['fechaHora'])) 
        {
            return $this->fail('QR inválido.');
        }

        $user = User::find($payload['id']);

        try 
        {
            $qrTime = Carbon::parse($payload['fechaHora']);
        } catch (\Throwable) 
        {
            $this->notifyUser(
                $user,
                'Parking+',
                'Fecha/hora inválida en el código QR.',
                ['event' => 'qr_error','code' => 'date_invalid']
            );

            return $this->fail('Fecha/hora inválida en el QR.');
        }

        if ($qrTime->diffInSeconds(now()) > 15) 
        {
            $this->notifyUser(
                $user,
                'Parking+',
                'QR expirado. Vuelve a generar el código en la app (15s).',
                ['event' => 'qr_error','code'  => 'expired']
            );

            return $this->fail('QR expirado. Vuelve a generar el código (15s).');
        }

        if (! $user) 
        {
            return $this->fail('Usuario no encontrado.');
        }

        $parking = auth()->user()->parking;
        $readerIds = $parking->qrReaders()->pluck('id');

        if (! isset($payload['lat'], $payload['lng'])) 
        {
            $this->notifyUser(
                $user,
                'Parking+',
                'Enciende la ubicación en el dispositivo',
                ['event' => 'qr_error','code'  => 'no_location']
            );

            return $this->fail('El QR no contiene información de ubicación del dispositivo.');
        }

        $deviceLat = (float) $payload['lat'];
        $deviceLng = (float) $payload['lng'];

        $parkingLat = (float) ($parking->latitude_coordinate ?? 0);
        $parkingLng = (float) ($parking->longitude_coordinate ?? 0);

        if ($parkingLat !== 0.0 || $parkingLng !== 0.0) 
        {
            $distanceKm = $this->distanceInKm(
                $deviceLat,
                $deviceLng,
                $parkingLat,
                $parkingLng
            );

            if ($distanceKm > 2.0) 
            {
                $this->notifyUser(
                    $user,
                    'Parking+',
                    'El código se generó lejos del estacionamiento.',[
                        'event'    => 'qr_error',
                        'code'     => 'too_far',
                        'distance' => (string) round($distanceKm, 2)
                    ]
                );

                return $this->fail('El código QR se generó demasiado lejos del estacionamiento.');
            }
        }

        $openTx = Transaction::where('id_user', $user->id)
            ->whereNull('departure_date')
            ->whereIn('id_qr_reader', $readerIds)
            ->latest('id')
            ->first();

        if ($reader->sense === 1 && ! $openTx) 
        {
            $this->notifyUser(
                $user,
                'Parking+',
                'Este lector es de salida y no tienes una entrada abierta.',
                ['event' => 'error','code'  => 'no_open_entry']
            );

            return $this->fail('Este lector es de salida y el usuario no tiene entrada abierta.');
        }

        if ($reader->sense === 0 && $openTx) 
        {
            $this->notifyUser(
                $user,
                'Parking+',
                'Este lector es de entrada y ya tienes una estancia abierta.',
                ['event' => 'error','code'  => 'already_open_entry']
            );

            return $this->fail('Este lector es de entrada y el usuario ya tiene una estancia abierta.');
        }

        if (! $openTx) 
        {
            $recentEntry = Transaction::where('id_user', $user->id)
                ->whereNull('departure_date')
                ->whereIn('id_qr_reader', $readerIds)
                ->where(
                    'entry_date', 
                    '>=', 
                    now()->subSeconds(3)
                )
                ->exists();

            if ($recentEntry) 
            {
                return $this->silent('recent_entry');
            }

            $lastTx = Transaction::where('id_user', $user->id)
                ->whereIn('id_qr_reader', $readerIds)
                ->latest('id')
                ->first();

            if ($lastTx && $lastTx->departure_date && $lastTx->departure_date->diffInSeconds(now()) < 5) 
            {
                return $this->silent('post_exit_bounce');
            }

            $parkingType = (int) $parking->type;

            if ($parkingType === 2) 
            {
                $this->notifyUser(
                    $user,
                    'Parking+',
                    'Elige cómo deseas que se cobre',[
                        'event' => 'choose_billing_mode',
                        'parking_id' => (string) $parking->id,
                        'qr_reader_id' => (string) $reader->id,
                        'qr_timestamp' => $qrTime->toDateTimeString(),
                        'price_hour' => (string) ($parking->price ?? 0),
                        'price_flat' => (string) ($parking->price_flat ?? $parking->price ?? 0)
                    ]
                );

                return $this->ok('Entrada pendiente de confirmación por el usuario.', ['event' => 'entry_pending']);
            }

            $billingMode = $parkingType === 0 ? 'flat' : 'hour';

            $tx = Transaction::create([
                'amount' => null,
                'entry_date' => now(),
                'departure_date' => null,
                'id_qr_reader' => $reader->id,
                'id_user' => $user->id,
                'billing_mode' => $billingMode
            ]);

            $this->notifyUser(
                $user,
                'Parking+',
                'Entrada registrada correctamente.',[
                    'event' => 'entry',
                    'tx_id' => (string) $tx->id,
                    'mode' => $billingMode,
                    'parking' => (string) $parking->id
                ]
            );

            return $this->ok('Entrada registrada', [
                'event' => 'entry',
                'transaction_id' => $tx->id,
                'when' => $tx->entry_date->toDateTimeString()
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
            $openTx,
            $minutes
        );

        if (! $user->hasEnoughBalance($charge)) 
        {
            
            $this->notifyUser(
                $user,
                'Parking+',
                'Saldo insuficiente para completar el pago.',
                ['event'  => 'no_funds','charge' => (string) $charge]
            );

            return $this->fail('Saldo insuficiente para completar el pago.');
        }

        try 
        {
            DB::transaction(function () use ($user, $charge, $openTx, $reader, $parking) 
            {
                $affected = User::whereKey($user->id)
                    ->where(
                        'amount', 
                        '>=', 
                        $charge
                    )
                    ->decrement('amount', $charge);

                if ($affected !== 1) 
                {
                    throw new \RuntimeException('NO_FUNDS');
                }

                User::whereKey($parking->id_user)
                    ->increment('amount', $charge);

                $openTx->update([
                    'amount' => round($charge, 2),
                    'departure_date' => now(),
                    'id_qr_reader' => $reader->id
                ]);
            });
        } catch (\RuntimeException $exception) 
        {
            if ($exception->getMessage() === 'NO_FUNDS') 
            {
                $this->notifyUser(
                    $user,
                    'Parking+',
                    'Saldo insuficiente para completar el pago.',
                    ['event' => 'no_funds','charge' => (string) $charge]
                );

                return $this->fail('Saldo insuficiente para completar el pago.');
            }
            throw $exception;
        }

        $this->notifyUser(
            $user,
            'Parking+',
            'Salida registrada. Monto cobrado: $' . number_format($charge, 2), [
                'event' => 'exit',
                'tx_id' => (string) $openTx->id,
                'charged' => (string) $charge,
                'minutes' => (string) $minutes
            ]
        );

        return $this->ok('Salida registrada', [
            'event' => 'exit',
            'transaction_id' => $openTx->id,
            'charged' => $charge,
            'minutes' => $minutes
        ]);
    }

    private function ensureOwnership(QrReader $reader): void
    {
        $parking = auth()->user()->parking;

        $authorized = $parking && $reader->id_parking === $parking->id;

        if (! $authorized) 
        {
            if (request()->expectsJson()) 
            {
                abort(response()->json(['ok' => false,'message' => 'No autorizado.'], 403));
            }

            abort(403, 'No autorizado.');
        }
    }

    private function computeCharge(User $user, $parking, Transaction $tx, int $minutes): float
    {
        $minutes = max(1, $minutes);

        $priceHour = max(0.0, (float) ($parking->price ?? 0));
        $priceFlat = max(0.0, (float) ($parking->price_flat ?? $parking->price ?? 0));

        $type = (int) $parking->type;
        $mode = $tx->billing_mode;

        switch ($type) 
        {
            case 0:
                $raw = $priceFlat;
                break;

            case 1:
                $hours = max(1, ceil($minutes / 60));
                $raw = $hours * $priceHour;
                break;

            case 2:
                $effectiveMode = $mode === 'flat' ? 'flat' : 'hour';
                if ($effectiveMode === 'flat') 
                {
                    $raw = $priceFlat;
                } else 
                {
                    $hours = max(1, ceil($minutes / 60));
                    $raw = $hours * $priceHour;
                }
                break;

            default:
                $raw = $priceFlat;
        }

        $uct = $user->activeUserClientTypeForParking((int) $parking->id);

        if ($uct && $uct->clientType) 
        {
            $ct = $uct->clientType;

            if ((int) $ct->discount_type === 0) 
            {
                $pct = min(100.0, max(0.0, (float) $ct->amount));
                $raw -= round($raw * ($pct / 100), 2);
            } elseif ((int) $ct->discount_type === 1) 
            {
                $raw -= (float) $ct->amount;
            }
        }

        return round(max(0, $raw), 2);
    }


    private function ok(string $msg, array $data = []): JsonResponse
    {
        return response()->json([
            'ok'      => true,
            'message' => $msg,
            'data'    => $data
        ]);
    }

    private function silent(string $reason): JsonResponse
    {
        return response()->json([
            'ok'     => true,
            'silent' => true,
            'reason' => $reason
        ]);
    }

    private function fail(string $msg, int $code = 422): JsonResponse
    {
        return response()->json(['ok' => false, 'message' => $msg], $code);
    }

    private function notifyUser(?User $user, string $title, string $body, array $data = []): void
    {
        if (! $user || empty($user->notification_token)) 
        {
            return;
        }

        $this->firebase->sendNotification(
            $user->notification_token,
            $title,
            $body,
            $data
        );
    }

    private function distanceInKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371.0;

        $lat1 = deg2rad($lat1);
        $lon1 = deg2rad($lon1);
        $lat2 = deg2rad($lat2);
        $lon2 = deg2rad($lon2);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) ** 2
            + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}