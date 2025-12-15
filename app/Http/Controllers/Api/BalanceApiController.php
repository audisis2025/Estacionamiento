<?php
/*
* Nombre de la clase         : BalanceApiController.php
* Descripción de la clase    : Controlador para administrar el saldo de los usuarios.
* Fecha de creación          : 05/11/2025
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 05/11/2025
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class BalanceApiController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        if (!$userId) 
        {
            return response()->json([
                'icon'  => 'error',
                'title' => 'No autorizado',
                'text'  => 'Debes iniciar sesión para consultar tu saldo.'
            ], 401);
        }

        $amount = User::whereKey($userId)->value('amount') ?? 0;

        return response()->json([
            'icon'    => 'success',
            'title'   => 'Saldo actual',
            'balance' => (float) $amount
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            [ 'amount' => ['required', 'numeric', 'min:1']],
            [
                'required' => 'El campo :attribute es obligatorio.',
                'numeric'  => 'El campo :attribute debe ser numérico.',
                'min'      => 'El campo :attribute debe ser al menos :min.'
            ],
            ['amount' => 'monto']
        );

        $userId = Auth::id();

        if (!$userId) 
        {
            return response()->json([
                'icon'  => 'error',
                'title' => 'No autorizado',
                'text'  => 'Debes iniciar sesión para realizar una recarga.'
            ], 401);
        }

        User::whereKey($userId)->increment('amount', $validated['amount']);

        $newBalance = (float) (User::whereKey($userId)->value('amount') ?? 0);

        return response()->json([
            'icon' => 'success',
            'title' => '¡Recarga exitosa!',
            'text' => 'Tu saldo se ha actualizado correctamente.',
            'new_balance' => $newBalance
        ], 200);
    }
}
