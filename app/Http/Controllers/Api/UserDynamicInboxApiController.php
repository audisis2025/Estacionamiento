<?php
/*
* Nombre de la clase         : UserDynamicInboxApiController.php
* Descripción de la clase    : Controlador para administrar solicitudes de usuarios dinamicos.
* Fecha de creación          : 10/01/2026
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 10/01/2026
* Autorizó                   : Elian Pérez
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
 
class UserDynamicInboxApiController extends Controller
{
    public function sendRequest(Request $request, $parkingId)
    {
        $user = $request->user();
 
        $request->validate(['client_type_id' => 'required|integer|exists:client_types,id']);
 
        $clientTypeId = $request->client_type_id;
 
        $parking = Parking::with('clientTypes')->find($parkingId);
        if (!$parking)
        {
            return response()->json(['error' => 'Estacionamiento no encontrado'], 404);
        }
 
        if (!$parking->clientTypes->where('id', $clientTypeId)->count())
        {
            return response()->json(['error' => 'Tipo de cliente inválido'], 422);
        }
 
        $existing = UserClientType::where('id_user', $user->id)
            ->whereHas('clientType', function ($q) use ($parkingId)
            {
                $q->where('id_parking', $parkingId);
            })
            ->whereIn('approval', [0, 1])
            ->first();
 
        if ($existing)
        {
            return response()->json(['error' => 'Ya tienes una solicitud pendiente o aprobada en este estacionamiento'], 409);
        }
 
        $record = UserClientType::create([
            'id_user' => $user->id,
            'id_client_type' => $clientTypeId,
            'approval' => 0
        ]);
 
        return response()->json([
            'status'  => 'success',
            'message' => 'Solicitud enviada correctamente',
            'request' => $record
        ], 201);
    }
 
    public function userApproved(Request $request)
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
 
    public function userPending(Request $request)
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
                'has_approved' => $hasApproved,
            ];
        });
 
        return response()->json(['status'   => 'success', 'parkings' => $parkings]);
    }
}