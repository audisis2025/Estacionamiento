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

class UserParkingRequestApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) 
        {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $parkings = Parking::with('clientTypes')->get()->map(function ($parking) use ($user) 
        {

            $existing = UserClientType::where('id_user', $user->id)
                ->whereHas('clientType', function ($q) use ($parking) 
                {
                    $q->where('id_parking', $parking->id);
                })
                ->first();

            $hasPending  = $existing && $existing->approval == 0;
            $hasApproved = $existing && $existing->approval == 1;

            return [
                'id'    => $parking->id,
                'name'  => $parking->name,
                'client_types' => $parking->clientTypes->map(function ($t) 
                {
                    return ['id' => $t->id, 'type_name' => $t->type_name];
                }),
                'has_pending'  => $hasPending,
                'has_approved' => $hasApproved
            ];
        });

        return response()->json(['status' => 'success', 'parkings' => $parkings]);
    }
}
