<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Role;
use App\Models\User;
use App\Models\UserClientType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class RegisterProviderApiController extends Controller
{
    public function registerProvider(Request $request)
    {
        $data = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', Password::min(8)],
            'phone_number'   => ['required', 'digits:10', 'unique:users,phone_number'],
            'id_client_type' => ['required', 'integer', 'exists:client_types,id'],
        ]);

    
        // 🔹 Plan básico
        $defaultPlan = Plan::updateOrCreate(
            ['type' => 'user', 'name' => 'Plan Básico'],
            [
                'price' => 0,
                'duration_days' => 30,
                'description' => 'Acciones limitadas para usuarios gratuitos.',
            ]
        );

        // 🔹 Crear usuario normal
        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'phone_number' => $data['phone_number'],
            'id_plan'      => $defaultPlan->id,
        ]);

        // 🔹 Crear registro pendiente en user_client_types
        UserClientType::create([
            'approval'        => 0, // pendiente
            'expiration_date' => null,
            'id_user'         => $user->id,
            'id_client_type'  => $data['id_client_type'],
        ]);

        return response()->json([
            'message' => 'provider_registered_pending',
            'user' => [
                'id'          => $user->id,
                'name'        => $user->name,
                'email'       => $user->email,
                'role'        => 'usuario',
                'plan'        => $defaultPlan->name,
            ],
            'status' => 'awaiting_approval',
        ], 201);
    }
}
