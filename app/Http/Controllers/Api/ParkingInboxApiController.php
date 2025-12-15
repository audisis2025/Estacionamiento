<?php
/*
* Nombre de la clase         : ParkingInboxApiController.php
* Descripción de la clase    : Controlador para administrar la bandeja de entrada de los usuarios en relación a 
                               los estacionamientos.
* Fecha de creación          : 27/11/2025
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 27/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento :
* Responsable                : 
* Revisor                    : 
*/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parking;
use App\Models\UserClientType;
use Illuminate\Http\Request;
 
class ParkingInboxApiController extends Controller
{
    public function sendRequest(Request $request, $parkingId)
    {
        $user = $request->user();
 
        $request->validate(['client_type_id' => 'required|integer|exists:client_types,id']);
 
        $clientTypeId = $request->client_type_id;
 
        $parking = Parking::with('clientTypes')->find($parkingId);
        if (!$parking) 
        {
            return response()->json(['error' => 'Estacionamiento no encontrado'], 404);
        }

        if (!$parking->clientTypes->where('id', $clientTypeId)->count()) 
        {
            return response()->json(['error' => 'Tipo de cliente inválido'], 422);
        }

        $existing = UserClientType::where('id_user', $user->id)
            ->whereHas('clientType', function ($q) use ($parkingId) 
            {
                $q->where('id_parking', $parkingId);
            })
            ->whereIn('approval', [0, 1])
            ->first();

        if ($existing) 
        {
            return response()->json(['error' => 'Ya tienes una solicitud pendiente o aprobada en este estacionamiento'], 409);
        }

        $record = UserClientType::create([
            'id_user' => $user->id,
            'id_client_type' => $clientTypeId,
            'approval' => 0
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Solicitud enviada correctamente',
            'request' => $record
        ], 201);
    }
}
