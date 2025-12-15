<?php

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

        $response = $approved->map(function ($item) {
            $clientType = $item->clientType;

            return [
                'id'            => $item->id,
                'parking_id'    => $clientType->id_parking,
                'parking_name'  => $clientType->parking?->name ?? '',
                'type_name'     => $clientType->type_name ?? '',
                'discount_type' => $clientType->discount_type ?? 0,
                'amount'        => $clientType->amount ?? 0,
                'discount_label'=> $this->getDiscountLabel(
                    $clientType->discount_type ?? 0,
                    $clientType->amount ?? 0
                ),
            ];
        });

        return response()->json([
            'status'   => 'success',
            'count'    => $response->count(),
            'approved' => $response,
        ]);
    }

    /**
     * 🏷️ Genera etiqueta legible del descuento
     * 0 = Porcentaje | 1 = Monto fijo
     */
    private function getDiscountLabel(int $discountType, float $amount): string
    {
        return match ($discountType) {
            0 => "{$amount}% de descuento",
            1 => "$" . number_format($amount, 2) . " MXN de descuento",
            default => "Sin descuento",
        };
    }
}