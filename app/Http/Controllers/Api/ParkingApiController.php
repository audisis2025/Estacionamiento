<?php
 
namespace App\Http\Controllers\Api;
 
use App\Http\Controllers\Controller;
use App\Models\Parking;
use Illuminate\Http\Request;
 
class ParkingApiController extends Controller
{
    public function nearby(Request $request)
    {
        $lat = $request->query('lat');
        $lng = $request->query('lng');
 
        if (!$lat || !$lng) {
            return response()->json(['error' => 'Coordenadas no enviadas'], 422);
        }
 
        $radius = 0.05;
 
        $parkings = Parking::whereBetween('latitude_coordinate', [$lat - $radius, $lat + $radius])
            ->whereBetween('longitude_coordinate', [$lng - $radius, $lng + $radius])
            ->with([
                'schedules:id,opening_time,closing_time,id_day,id_parking',
            ])
            ->get([
                'id',
                'name',
                'latitude_coordinate',
                'longitude_coordinate',
                'type',
                'price',       // price_hour o price (si 1 o 2)
                'price_flat',  // fijo
            ]);
 
        return response()->json(['parkings' => $parkings]);
    }
 
 
    public function withDynamicClients()
    {
        $parkings = Parking::whereHas('clientTypes', function ($q) {
            $q->whereHas('userClientTypes');
        })->withCount('clientTypes')
            ->get(['id', 'name']);
 
        return response()->json($parkings);
    }
 
    public function clientTypesByParking($id)
    {
        $parking = Parking::with(['clientTypes:id,typename,id_parking'])
            ->findOrFail($id);
 
        return response()->json([
            'parking' => $parking->name,
            'client_types' => $parking->clientTypes,
        ]);
    }
}