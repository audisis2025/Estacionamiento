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
        ]);

        $role = Role::where('name', 'usuario')->firstOrFail();
        $plan = Plan::where('name', 'Plan Básico')->firstOrFail();

        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
            'phone_number' => $data['phone_number'],
            'id_role'      => $role->id,
            'id_plan'      => $plan->id,
            'is_active'    => true,
        ]);

        return response()->json([
            'message' => 'registered',
            'user' => [
                'id'              => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'role_id'         => $role->id,
                'role_name'       => 'usuario',
                'plan_id'         => $plan->id,
                'plan_name'       => $plan->name,
                'is_active'       => $user->is_active,
                'has_active_plan' => true, // Plan básico siempre activo
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

        $user = Auth::user();

        // Verificar si el usuario está bloqueado (is_active = false)
        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'message' => 'user_blocked',
                'error'   => 'Tu cuenta ha sido bloqueada. Contacta al administrador.',
            ], 423);
        }

        // Verificar que sea un usuario normal (no admin o parking admin)
        if ($user->id_role !== null && !$user->role?->isUser()) {
            Auth::logout();
            return response()->json([
                'message' => 'role_not_allowed',
                'error'   => 'Esta cuenta solo puede iniciar sesión desde el panel web.',
            ], 403);
        }

        $token = $user->createToken(
            $request->header('X-Device-Name') ?? 'flutter-app'
        )->plainTextToken;

        return response()->json([
            'message'    => 'authenticated',
            'token'      => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id'              => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'role_id'         => $user->id_role,
                'role_name'       => $user->role?->name,
                'is_active'       => $user->is_active,
                'has_active_plan' => $user->hasActivePlan(),
            ],
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user()->load('plan', 'role');

        // Verificar si el usuario está bloqueado
        if (!$user->is_active) {
            return response()->json([
                'message' => 'user_blocked',
                'error'   => 'Tu cuenta ha sido bloqueada. Contacta al administrador.',
            ], 423);
        }

        return response()->json([
            'user' => [
                'id'              => $user->id,
                'name'            => $user->name,
                'email'           => $user->email,
                'phone_number'    => $user->phone_number,
                'role_id'         => $user->id_role,
                'role_name'       => $user->role?->name,
                'is_active'       => $user->is_active,
                'has_active_plan' => $user->hasActivePlan(),
                'plan_id'         => $user->plan?->id,
                'plan_name'       => $user->plan?->name,
                'end_date'        => $user->end_date?->format('Y-m-d'),
                'amount'          => $user->amount,

                'plan' => $user->plan ? [
                    'id'            => $user->plan->id,
                    'name'          => $user->plan->name,
                    'price'         => $user->plan->price,
                    'duration_days' => $user->plan->duration_days,
                    'description'   => $user->plan->description,
                ] : null,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'logged_out']);
    }
}