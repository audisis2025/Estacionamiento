<?php
/*
* Nombre de la clase         : ParkingApiController.php
* Descripción de la clase    : Controlador para administrar las entradas de los usuarios.
* Fecha de creación          : 06/11/2025
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 06/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 2.0
* Fecha de mantenimiento     : 10/01/2026
* Folio de mantenimiento     : L0025
* Tipo de mantenimiento      : Correctivo
* Descripción del mantenimiento : Mejorar la detección de estacionamientos cercanos al usuario
* Responsable                : Jonathan Díaz
* Revisor                    : Angel Davila
*/
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

        if (!$lat || !$lng) 
        {
            return response()->json(['error' => 'Coordenadas no enviadas'], 422);
        }

        $radiusKm = (float) ($request->query('radius_km', 5));

        $haversine = "(6371 * acos(
            cos(radians(?)) * cos(radians(latitude_coordinate)) *
            cos(radians(longitude_coordinate) - radians(?)) +
            sin(radians(?)) * sin(radians(latitude_coordinate))
        ))";

        $parkings = Parking::query()
            ->select([
                'id',
                'name',
                'latitude_coordinate',
                'longitude_coordinate',
                'type',
                'price',
                'price_flat'
            ])
            ->selectRaw("$haversine AS distance_km", [$lat, $lng, $lat])
            ->with(['schedules:id,opening_time,closing_time,id_day,id_parking'])
            ->whereNotNull('latitude_coordinate')
            ->whereNotNull('longitude_coordinate')
            ->having('distance_km', '<=', $radiusKm)
            ->orderBy('distance_km', 'asc')
            ->get();

        return response()->json(['parkings' => $parkings]);
    }
}