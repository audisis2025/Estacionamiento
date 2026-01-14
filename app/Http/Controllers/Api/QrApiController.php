<?php
/*
* Nombre de la clase         : QrApiController.php
* Descripción de la clase    : Controlador para administrar el qr del usuario.
* Fecha de creación          : 10/01/2026
* Elaboró                    : Jonathan Diaz
* Fecha de liberación        : 10/01/2026
* Autorizó                   : Elian Pérez
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
 
class QrApiController extends Controller
{
    public function generateQr(Request $request)
    {
        $request->validate(['lat' => 'required|numeric', 'lng' => 'required|numeric']);
 
        $user = Auth::user();
 
        $payload = [
            'id' => $user->id,
            'fechaHora' => now()->format('Y-m-d H:i:s'),
            'lat' => $request->lat,
            'lng' => $request->lng,
        ];
 
        return response()->json($payload, 200);
    }
}