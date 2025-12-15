<?php
/*
* Nombre de la clase         : UserApprovedTypesApiController.php
* Descripción de la clase    : Controlador para administrar los tipos de cliente aprobados de 
                               los usuarios en relación alos estacionamientos.
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
use App\Models\UserClientType;
use Illuminate\Http\Request;

class UserApprovedTypesApiController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $approved = UserClientType::where('id_user', $user->id)
            ->approved()
            ->whereHas('clientType')
            ->with('clientType.parking')
            ->get();

        $response = $approved->map(function ($item) 
        {
            $clientType = $item->clientType;

            return [
                'id' => $item->id,
                'parking_id' => $clientType->id_parking,
                'parking_name' => $clientType->parking?->name ?? '',
                'type_name' => $clientType->type_name ?? '',
                'discount_type' => $clientType->discount_type ?? 0,
                'amount' => $clientType->amount ?? 0,
                'discount_label'=> $this->getDiscountLabel($clientType->discount_type ?? 0, $clientType->amount ?? 0)
            ];
        });

        return response()->json([
            'status' => 'success',
            'count' => $response->count(),
            'approved' => $response
        ]);
    }

    private function getDiscountLabel(int $discountType, float $amount): string
    {
        return match ($discountType) 
        {
            0 => "{$amount}% de descuento",
            1 => "$" . number_format($amount, 2) . " MXN de descuento",
            default => "Sin descuento"
        };
    }
}