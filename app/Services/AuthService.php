<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Intenta autenticar al usuario y devuelve el token Sanctum.
     */
    public function login(string $email, string $password): string
    {
        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) 
        {
            return 'No autorizado';
        }
        return 'Autorizado';
    }
}
