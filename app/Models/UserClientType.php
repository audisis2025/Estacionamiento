<?php
/*
* Nombre de la clase         : UserClientType.php
* Descripción de la clase    : Modelo Eloquent para la tabla 'user_client_types', que registra la relación de un 
                               usuario con un tipo de cliente específico.
* Fecha de creación          : 02/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 02/11/2025
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

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class UserClientType extends Model
{
    protected $table = 'user_client_types';
    public $timestamps = false;

    protected $fillable = [
        'approval',
        'id_user',
        'id_client_type'
    ];

    protected $casts = ['approval' => 'integer'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class, 
            'id_user', 
            'id'
        );
    }

    public function clientType(): BelongsTo
    {
        return $this->belongsTo(
            ClientType::class, 
            'id_client_type', 
            'id'
        );
    }

    public function parking(): ?HasOneThrough
    {
        return $this->clientType?->parking();
    }

    public function scopePending($q): Builder
    {
        return $q->where('approval', 0);
    }
    public function scopeApproved($q): Builder
    {
        return $q->where('approval', 1);
    }
}
