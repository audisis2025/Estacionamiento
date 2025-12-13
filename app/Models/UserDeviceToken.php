<?php
/*
* Nombre de la clase         : UserDeviceToken.php
* Descripción de la clase    : Modelo Eloquent para la tabla 'user_device_tokens', que almacena 
                               los tokens FCM para notificaciones push de cada dispositivo de usuario.
* Fecha de creación          : 04/12/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 04/12/2025
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
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDeviceToken extends Model
{
    protected $fillable = [
        'user_id', 
        'device_name', 
        'platform', 
        'fcm_token', 
        'last_used_at'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
