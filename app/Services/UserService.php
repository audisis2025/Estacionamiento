<?php
/*
* Nombre de la clase         : UserService.php
* Descripción de la clase    : Servicio para actualizacion del token de notificaciones de firebase
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

class UserService
{
    public function UpdateNotificationToken(int $id, array $data)
    {
        $user = User::findOrFail($id);
        return $user->update(['notification_token'=> $data ['notification_token']]);
    }
}
