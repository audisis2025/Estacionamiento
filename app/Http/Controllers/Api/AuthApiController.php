<?php
/*
* Nombre de la clase         : AuthApiController.php
* Descripción de la clase    : Controlador para administrar la autenticación de usuarios.
* Fecha de creación          : 03/11/2025
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 03/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 2.0
* Fecha de mantenimiento     : 22/12/2025
* Folio de mantenimiento     : L0019
* Tipo de mantenimiento      : Correctivo
* Descripción del mantenimiento : Agregación de médodo de registro y mejoras en el código
* Responsable                : Jonathan Diaz
* Revisor                    : Angel Davila
*/
namespace App\Http\Controllers\Api;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
 
class AuthApiController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'password' => ['required', Password::min(8)],
            'phone_number' => [
                'required',
                'digits:10',
                'unique:users,phone_number'
            ]
        ]);
 
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number'],
            'id_role' => 3,
            'id_plan' => 4
        ]);
 
        return response()->json(['message' => 'registered'], 201);
    }
 
    public function dynamicRegister(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email'
            ],
            'password' => ['required', Password::min(8)],
            'phone_number' => [
                'required',
                'digits:10',
                'unique:users,phone_number'
            ]
        ]);
 
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number']
        ]);
 
        return response()->json(['message' => 'provider_registered'], 201);
    }
 
    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required', 'string']]);
 
        if (!Auth::attempt($credentials))
        {
            return response()->json(['message' => 'invalid_credentials'], 422);
        }
 
        $user = Auth::user();
 
        if (!$user->is_active)
        {
            return response()->json(['message' => 'user_blocked'], 423);
        }
 
        if ($user->id_role !== null && !$user->role?->isUser())
        {
            return response()->json(['message' => 'role_not_allowed'], 403);
        }
 
        $token = $user->createToken($request->header('X-Device-Name') ?? 'flutter-app')->plainTextToken;
 
        return response()->json([
            'message' => 'authenticated',
            'token' => $token,
            'token_type' => 'Bearer'
        ]);
    }
 
    public function me(Request $request)
    {
        $user = $request->user()->load('plan', 'role');
 
        if (!$user->is_active)
        {
            return response()->json(['message' => 'user_blocked'], 423);
        }
 
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'is_active' => $user->is_active,
                'has_active_plan' => $user->hasActivePlan(),
                'plan_id' => $user->plan?->id,
                'plan_name' => $user->plan?->name,
                'end_date' => $user->end_date?->format('Y-m-d'),
                'amount' => $user->amount,

                'plan' => $user->plan ? [
                    'id' => $user->plan->id,
                    'name' => $user->plan->name,
                    'price' => $user->plan->price,
                    'duration_days' => $user->plan->duration_days,
                    'description' => $user->plan->description
                ] : null
            ]
        ]);
    }
 
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'logged_out']);
    }
}