<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
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
            'type'         => ['nullable', 'string', 'in:usuario,admin,adminEstacionamiento'],
        ]);

        $roleName = $data['type'] ?? 'usuario';
        $roleId = Role::where('name', $roleName)->value('id') ?? 3;

        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'phone_number' => $data['phone_number'],
            'id_role'      => $roleId,
        ]);

        $device = $request->header('X-Device-Name') ?: 'flutter-app';
        $token  = $user->createToken($device, ['*'])->plainTextToken;

        return response()->json([
            'message' => 'registered',
            'token'   => $token,
            'token_type' => 'Bearer',
            'user'    => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'id_role' => $user->id_role,
                'role_name' => $roleName,
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
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'id_role' => $user->id_role,
                'role_name' => $user->role->name ?? 'usuario',
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'logged_out']);
    }
}
