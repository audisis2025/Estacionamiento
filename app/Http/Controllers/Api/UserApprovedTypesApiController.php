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
            ->where('approval', 1)
            ->with(['clientType.parking'])
            ->get();
 
        $response = $approved->map(function ($item) {
            return [
                'parking_id'      => $item->clientType->id_parking,
                'parking_name'    => $item->clientType->parking->name,
                'type_name'       => $item->clientType->type_name,
                'expiration_date' => optional($item->expiration_date)->format('Y-m-d'),
            ];
        });
 
        return response()->json([
            'status'   => 'success',
            'count'    => $response->count(),
            'approved' => $response
        ]);
    }
}