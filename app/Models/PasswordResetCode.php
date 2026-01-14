<?php
/*
* Nombre de la clase         : PasswordResetCode.php
* Descripción de la clase    : Modelo Eloquent para la tabla 'password_reset_tokens', que representa los codigos para restablecer la contraseña
                               de los usuarios de movil
* Fecha de creación          : 14/12/2025
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 14/12/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetCodeMail;

class PasswordResetCode extends Model
{
    public static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }
    public static function createForEmail(string $email): void
    {
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        
        $code = self::generateCode();
        
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $code,
            'created_at' => now(),
        ]);

        Mail::to($email)->send(new PasswordResetCodeMail($code));
    }

    public static function verify(string $email, string $code): bool
    {
        return DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $code)
            ->where(
                'created_at', 
                '>', 
                now()->subMinutes(15)
            )
            ->exists();
    }

    public static function consume(string $email, string $code): void
    {
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $code)
            ->delete();
    }
}
