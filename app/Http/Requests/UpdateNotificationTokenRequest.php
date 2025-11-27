<?php
/*
* Nombre de la clase         : UpdateNotificationTokenRequest.php
* Descripción de la clase    : Valida la solicitud para actualizar el token de notificación del usuario (Firebase Cloud Messaging).
* Fecha de creación          : 20/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 20/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNotificationTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return ['notification_token' => 'required|string|max:255'];
    }
}
