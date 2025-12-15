<?php
/*
* Nombre de la clase         : ParkingInboxApiController.php
* Descripción de la clase    : Controlador para administrar la bandeja de entrada de los usuarios en relación a 
                               los estacionamientos.
* Fecha de creación          : 05/11/2025
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 05/11/2025
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
use App\Models\Plan;

class PlanApiController extends Controller
{
    public function index()
    {
        $plans = Plan::where('type', 'user')
            ->orderBy('price')
            ->orderBy('duration_days')
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $plans->count(),
            'data' => $plans
        ]);
    }

    public function show(Plan $plan)
    {
        abort_unless($plan->type === 'user', 404);
        return response()->json(['status' => 'success', 'data' => $plan]);
    }
}