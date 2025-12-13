<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetCodeMail;

class PasswordResetCode extends Model
{
        /**
     * Generar código aleatorio de 6 dígitos
     */
    public static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Crear y enviar código de reset
     */
    public static function createForEmail(string $email): void
    {
        // Eliminar códigos anteriores del mismo email
        DB::table('password_reset_tokens')->where('email', $email)->delete();
        
        // Generar nuevo código
        $code = self::generateCode();
        
        // Guardar en la tabla existente
        DB::table('password_reset_tokens')->insert([
            'email' => $email,
            'token' => $code,
            'created_at' => now(),
        ]);

        // Enviar email con el código
        Mail::to($email)->send(new PasswordResetCodeMail($code));
    }

    /**
     * Verificar si el código es válido
     */
    public static function verify(string $email, string $code): bool
    {
        return DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $code)
            ->where('created_at', '>', now()->subMinutes(15)) // Expira en 15 minutos
            ->exists();
    }

    /**
     * Eliminar código después de usarlo
     */
    public static function consume(string $email, string $code): void
    {
        DB::table('password_reset_tokens')
            ->where('email', $email)
            ->where('token', $code)
            ->delete();
    }
}
