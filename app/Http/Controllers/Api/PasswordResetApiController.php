<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordResetApiController extends Controller
{
    /**
     * Solicitar código de recuperación
     * POST /api/password/request-code
     */
    public function requestCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'No existe una cuenta con este correo electrónico.',
        ]);

        try {
            PasswordResetCode::createForEmail($request->email);

            return response()->json([
                'message' => 'Código enviado exitosamente. Revisa tu correo.',
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al enviar el código. Intenta nuevamente.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verificar código (opcional, para validar antes de cambiar contraseña)
     * POST /api/password/verify-code
     */
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $isValid = PasswordResetCode::verify(
            $request->email,
            $request->code
        );

        if (!$isValid) {
            return response()->json([
                'message' => 'Código inválido o expirado.',
                'valid' => false,
            ], 422);
        }

        return response()->json([
            'message' => 'Código válido.',
            'valid' => true,
        ], 200);
    }

    /**
     * Restablecer contraseña con código
     * POST /api/password/reset-with-code
     */
    public function resetWithCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'code' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Verificar código
        if (!PasswordResetCode::verify($request->email, $request->code)) {
            throw ValidationException::withMessages([
                'code' => ['El código es inválido o ha expirado.'],
            ]);
        }

        // Buscar usuario
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No se encontró un usuario con este correo.'],
            ]);
        }

        // Actualizar contraseña
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        // Consumir código (eliminarlo)
        PasswordResetCode::consume($request->email, $request->code);

        return response()->json([
            'message' => 'Contraseña actualizada exitosamente.',
        ], 200);
    }
}