<?php
/*
* Nombre de la clase         : ManualExitToken.php
* Descripción de la clase    : Modelo Eloquent para la tabla 'manual_exit_tokens', que representa los tokens de salida manual.
* Fecha de creación          : 10/12/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 10/12/2025
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

class ManualExitToken extends Model
{
    protected $table = 'manual_exit_tokens';

    protected $fillable = [
        'token',
        'transaction_id',
        'id_parking',
        'used_at'
    ];

    protected $dates = ['used_at'];

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(
            Transaction::class, 
            'transaction_id', 
            'id'
        );
    }

    public function parking(): BelongsTo
    {
        return $this->belongsTo(
            Parking::class, 
            'id_parking', 
            'id'
        );
    }
}
