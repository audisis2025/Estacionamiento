<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function __construct(private AuthService $authService) {}

    public function login(Request $request)
    {
        $validated = $request->validate
        (
            [
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]
        );

        $token = $this->authService->login
        (
            $validated['email'],
            $validated['password']
        );

        return response()->json
        (
            [
                'token' => $token,
                'user' => $request->user(),
            ]
        );
    }
}
