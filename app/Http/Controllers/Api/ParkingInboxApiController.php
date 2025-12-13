<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parking;
use App\Models\UserClientType;
use Illuminate\Http\Request;

class ParkingInboxApiController extends Controller
{
    
    public function getInboxParkings(Request $request)
    {
        $user = $request->user();

        $parkings = Parking::with('clientTypes')->get()->map(function ($parking) use ($user) {

            // 🔥 Verificar si el usuario YA tiene una solicitud pendiente en este parking
            $hasPending = UserClientType::where('id_user', $user->id)
                ->whereHas('clientType', function ($q) use ($parking) {
                    $q->where('id_parking', $parking->id);
                })
                ->where('approval', 0) // 0 = pendiente
                ->exists();

            return [
                'id'           => $parking->id,
                'name'         => $parking->name,
                'client_types' => $parking->clientTypes->map(function ($t) {
                    return [
                        'id'        => $t->id,
                        'type_name' => $t->type_name,
                    ];
                }),

                // 🔥 Campo nuevo: indica si debe bloquear la UI
                'has_pending'  => $hasPending,
            ];
        });

        return response()->json([
            'parkings' => $parkings
        ], 200);
    }

    public function sendRequest(Request $request, $parkingId)
    {
        $user = $request->user();

        $request->validate([
            'client_type_id' => 'required|integer|exists:client_types,id'
        ]);

        $clientTypeId = $request->client_type_id;

        // Verificar estacionamiento válido
        $parking = Parking::with('clientTypes')->find($parkingId);
        if (!$parking) {
            return response()->json(['error' => 'Estacionamiento no encontrado'], 404);
        }

        // Verificar que el tipo pertenezca al estacionamiento
        if (!$parking->clientTypes->where('id', $clientTypeId)->count()) {
            return response()->json(['error' => 'Tipo de cliente inválido'], 422);
        }

        // Verificar si YA tiene solicitud pendiente o aprobada
        $existing = UserClientType::where('id_user', $user->id)
            ->whereHas('clientType', function ($q) use ($parkingId) {
                $q->where('id_parking', $parkingId);
            })
            ->whereIn('approval', [0, 1]) // 0 = pendiente, 1 = aceptada
            ->first();

        if ($existing) {
            return response()->json([
                'error' => 'Ya tienes una solicitud pendiente o aprobada en este estacionamiento'
            ], 409);
        }

        // Crear solicitud nueva
        $record = UserClientType::create([
            'id_user'        => $user->id,
            'id_client_type' => $clientTypeId,
            'approval'       => 0, // pendiente
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Solicitud enviada correctamente',
            'request' => $record
        ], 201);
    }
}
