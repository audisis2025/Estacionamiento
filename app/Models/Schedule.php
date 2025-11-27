<?php
/*
* Nombre de la clase         : Schedule.php
* Descripción de la clase    : Modelo Eloquent para la tabla 'schedules', que define los horarios de apertura y cierre de un estacionamiento por día.
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

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $table = 'schedules';
    public $timestamps = false;

    protected $fillable = [
        'opening_time',
        'closing_time',
        'id_day',
        'id_parking'
    ];

    public function parking(): BelongsTo
    {
        return $this->belongsTo(Parking::class, 'id_parking', 'id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class, 'id_day', 'id');
    }
}
