<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parking;
use App\Models\UserClientType;
use Illuminate\Http\Request;

class UserParkingRequestApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $parkings = Parking::with('clientTypes')->get()->map(function ($parking) use ($user) {

            // Solicitud existente del usuario para este estacionamiento
            $existing = UserClientType::where('id_user', $user->id)
                ->whereHas('clientType', function ($q) use ($parking) {
                    $q->where('id_parking', $parking->id);
                })
                ->first();

            $hasPending  = $existing && $existing->approval == 0;
            $hasApproved = $existing && $existing->approval == 1;

            return [
                'id'    => $parking->id,
                'name'  => $parking->name,

                'client_types' => $parking->clientTypes->map(function ($t) {
                    return [
                        'id'   => $t->id,
                        'type_name' => $t->type_name,
                    ];
                }),

                // 🔥 Estos campos son los que Flutter necesita
                'has_pending'  => $hasPending,
                'has_approved' => $hasApproved,
            ];
        });

        return response()->json([
            'status'   => 'success',
            'parkings' => $parkings
        ]);
    }

}
