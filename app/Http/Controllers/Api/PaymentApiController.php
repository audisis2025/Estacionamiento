<?php
/*
* Nombre de la clase         : PaymentApiController.php
* Descripción de la clase    : Controlador para administrar la bandeja de entrada de los usuarios en relación a
                               los estacionamientos.
* Fecha de creación          : 06/11/2025
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 06/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 2.0
* Fecha de mantenimiento     : 11/01/2026
* Folio de mantenimiento     : L0027
* Tipo de mantenimiento      : Prefectivo
* Descripción del mantenimiento : Mejorar la consulta de las transacciones del usuario para emjorar la obtencion de los datos
* Responsable                : Jonathan Diaz
* Revisor                    : Angel Davila
*/
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
 
class PaymentApiController extends Controller
{
    public function history()
    {
        $user = Auth::user();
 
        $transactions = Transaction::with(['qrReader.parking'])
            ->where('id_user', $user->id)
            ->whereNotNull('departure_date')
            ->orderByDesc('id')
            ->get([
                'id',
                'amount',
                'entry_date',
                'departure_date',
                'id_qr_reader',
                'id_user'
            ]);
 
        $data = $transactions->map(function ($t)
        {
            $qrReader = $t->qrReader;
            $parking = $qrReader?->parking;
 
            return [
                'id' => $t->id,
                'amount' => (float) $t->amount,
                'entry_date' => $t->entry_date ? Carbon::parse($t->entry_date)->format('Y-m-d H:i:s') : null,
                'departure_date' => $t->departure_date ? Carbon::parse($t->departure_date)->format('Y-m-d H:i:s'): null,
                'parking' => ['name' => $parking->name ?? 'Desconocido']
            ];
        });
        return response()->json(['success' => true, 'transactions' => $data]);
    }
}