<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parking;
 
class UserParkingRequestApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
 
        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }
 
        $parkings = Parking::whereHas('clientTypes') // estacionamientos con tipos dinámicos
            ->whereDoesntHave('userClientTypes', function ($q) use ($user) {
                $q->where('id_user', $user->id)
                    ->whereIn('approval', [0, 1]); // pendiente o aceptado
            })
            ->with([
                'clientTypes:id,id_parking,type_name,discount_type,amount'
            ])
            ->get([
                'id',
                'name',
                'latitude_coordinate',
                'longitude_coordinate',
                'price',
                'type'
            ]);
 
        return response()->json([
            'status'   => 'success',
            'count'    => $parkings->count(),
            'parkings' => $parkings
        ]);
    }
}