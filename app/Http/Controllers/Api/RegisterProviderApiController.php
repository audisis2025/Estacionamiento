<?php
/*
* Nombre de la clase         : ParkingInboxApiController.php
* Descripción de la clase    : Controlador para administrar la bandeja de entrada de los usuarios en relación a 
                               los estacionamientos.
* Fecha de creación          : 06/11/2025
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 06/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento :
* Responsable                : 
* Revisor                    : 
*/
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

class RegisterProviderApiController extends Controller
{
    public function registerProvider(Request $request)
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

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone_number' => $data['phone_number']
        ]);

        return response()->json([
            'message' => 'provider_registered',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone_number' => $user->phone_number
            ]
        ], 201);
    }
}