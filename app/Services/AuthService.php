<?php
/*
* Nombre de la clase         : AuthService.php
* Descripción de la clase    : Service para validar las credenciales del usuario
* Fecha de creación          : 03/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 03/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService
{
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
