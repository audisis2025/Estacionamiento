<?php
/*
* Nombre de la clase         : FirebaseApiController.php
* Descripción de la clase    : Controlador para administrar las notificaciones de Firebase.
* Fecha de creación          : 27/11/2025
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 27/11/2025
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
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class FirebaseApiController extends Controller
{
    public function __construct(private FirebaseService $service)
    {

    }

    public function send(Request $request)
    {
        $request-> validate([
            'token' => 'required|string', 
            'title' => 'required|string', 
            'body' => 'required|string'
        ]);

        $response = $this-> service->sendNotification(
            $request-> token, 
            $request-> title, 
            $request-> body, 
            $request-> get('data', [])
        );

        return response()-> json($response);
    }

    public function updateNotificationToken(Request $request)
    {
        $request->validate(['notification_token' => ['required', 'string']]);

        $user = $request->user();
        $token = $request->input('notification_token');

        User::where('notification_token', $token)
            ->where(
                'id', 
                '!=', 
                $user->id
            )
            ->update(['notification_token' => null]);

        $user->update(['notification_token' => $token]);

        return response()->json(['ok' => true,'message' => 'Notification token actualizado.']);
    }
}
