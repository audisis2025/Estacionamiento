<?php
/*
* Nombre de la clase         : Day.php
* Descripción de la clase    : Modelo Eloquent para la tabla 'days', que representa los días de la semana o laborales.
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
use Illuminate\Database\Eloquent\Relations\HasMany;

class Day extends Model
{
    protected $table = 'days';
    public $timestamps = false;

    protected $fillable = ['name'];

    public function schedules(): HasMany
    {
        return $this->hasMany(
            Schedule::class, 
            'id_day', 
            'id'
        );
    }
}
