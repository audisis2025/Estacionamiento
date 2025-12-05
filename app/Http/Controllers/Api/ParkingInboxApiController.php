<?php
 
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
 
        $request->validate([
            'client_type_id' => 'required|integer|exists:client_types,id'
        ]);
 
        $clientTypeId = $request->client_type_id;
 
        // Validar estacionamiento
        $parking = Parking::with('clientTypes')->find($parkingId);
        if (!$parking) {
            return response()->json(['error' => 'Estacionamiento no encontrado'], 404);
        }
 
        // Validar que el tipo dinámico pertenezca al estacionamiento
        if (!$parking->clientTypes->where('id', $clientTypeId)->count()) {
            return response()->json(['error' => 'Tipo de cliente inválido'], 422);
        }
 
        // Verificar solicitudes existentes del usuario
        $existing = UserClientType::where('id_user', $user->id)
            ->whereHas('clientType', function ($q) use ($parkingId) {
                $q->where('id_parking', $parkingId);
            })
            ->whereIn('approval', [0, 1]) // pendiente o aceptado
            ->first();
 
        if ($existing) {
            return response()->json([
                'error' => 'Ya tienes una solicitud pendiente o aprobada en este estacionamiento'
            ], 409);
        }
 
        // Crear solicitud
        $record = UserClientType::create([
            'id_user'         => $user->id,
            'id_client_type'  => $clientTypeId,
            'approval'        => 0,      // pendiente
            'expiration_date' => null,   // null al solicitar
        ]);
 
        return response()->json([
            'status'  => 'success',
            'message' => 'Solicitud enviada correctamente',
            'request' => $record
        ], 201);
    }
}