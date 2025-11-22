<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class FirebaseApiController extends Controller
{
    public function __construct(private FirebaseService $service)
    {

    }

    public function send(Request $request)
    {
        $request-> validate([
            'token' => 'required|string', 
            'title' => 'required|string', 
            'body' => 'required|string',
        ]);

        $response = $this-> service->sendNotification(
            $request-> token, 
            $request-> title, 
            $request-> body, 
            $request-> get('data', []),
        );

        return response()-> json($response);
    }
}
