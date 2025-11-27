<?php
/*
* Nombre de la clase         : Plan.php
* Descripción de la clase    : Modelo Eloquent para la tabla 'plans', que representa los planes de suscripción 
                               disponibles.
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
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'description',
        'type'
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'duration_days' => 'integer',
    ];
    
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_plan', 'id');
    }

    public function scopeParking($q): Builder
    {
        return $q->where('type', 'parking');
    }

}
