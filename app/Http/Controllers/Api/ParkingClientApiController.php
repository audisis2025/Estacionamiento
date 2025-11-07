<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Parking;
use Illuminate\Http\Request;

class ParkingClientApiController extends Controller
{
    /**
     * Muestra los estacionamientos del usuario autenticado
     * que tienen clientes dinámicos (ClientType relacionados).
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        // 🔹 Solo estacionamientos del usuario con clientes dinámicos
        $parkings = Parking::where('id_user', $user->id)
            ->whereHas('clientTypes', function ($query) {
                $query->where('typename', 'LIKE', '%dinamico%');
            })
            ->with(['clientTypes:id,typename,id_parking'])
            ->get(['id', 'name', 'latitude_coordinate', 'longitude_coordinate', 'price', 'type']);

        return response()->json([
            'status' => 'success',
            'count' => $parkings->count(),
            'parkings' => $parkings,
        ]);
    }
}
