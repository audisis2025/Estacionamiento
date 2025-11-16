<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'     => ['required', Password::min(8)],
            'phone_number' => ['required', 'digits:10', 'unique:users,phone_number'],
            'type'         => ['nullable', 'string', 'in:usuario,admin,adminEstacionamiento,proveedor'],
        ]);

        $roleName = $data['type'] ?? 'usuario';

        $role = Role::firstOrCreate(['name' => $roleName], [
            'description' => ucfirst($roleName) . ' del sistema',
        ]);

        $defaultPlan = Plan::updateOrCreate(
            ['type' => 'user', 'name' => 'Plan Básico'],
            [
                'price' => 0,
                'duration_days' => 30,
                'description' => 'Acciones limitadas para usuarios gratuitos.',
            ]
        );

        // 🔹 Crear el usuario
        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'phone_number' => $data['phone_number'],
            'id_role'      => $role->id,
            'id_plan'      => $defaultPlan->id,
        ]);

        return response()->json([
            'message' => 'registered',
            'user'    => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role_id'    => $role->id,
                'role_name'  => $roleName,
                'plan_id'    => $defaultPlan->id,
                'plan_name'  => $defaultPlan->name,
            ],
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'invalid_credentials'], 422);
        }

        $user = User::where('email', $credentials['email'])->first();
        $device = $request->header('X-Device-Name') ?: 'flutter-app';
        $token  = $user->createToken($device, ['*'])->plainTextToken;

        return response()->json([
            'message' => 'authenticated',
            'token'   => $token,
            'token_type' => 'Bearer',
            'user'    => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role_id'   => $user->id_role,
                'role_name' => $user->role->name ?? 'usuario',
                'plan_id'   => $user->id_plan ?? null,
                'plan_name' => $user->plan->name ?? 'Gratis',
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('plan', 'role');

        return response()->json([
            'user' => [
                'id'         => $user->id,
                'name'       => $user->name,
                'email'      => $user->email,
                'role_id'    => $user->id_role,
                'role_name'  => $user->role->name ?? 'usuario',
                'plan_id'    => $user->plan->id ?? null,
                'plan_name'  => $user->plan->name ?? 'Gratis',
            ]
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'logged_out']);
    }
}
