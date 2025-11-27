<?php
/*
* Nombre de la clase         : ClientType.php
* Descripción de la clase    : Modelo Eloquent para la tabla 'client_types', que define los tipos de clientes y sus 
                               descuentos.
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
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientType extends Model
{
    protected $table = 'client_types';

    public $timestamps = false;

    protected $fillable = [
        'type_name',
        'discount_type',
        'amount',
        'id_parking'
    ];

    protected $casts = ['discount_type' => 'integer', 'amount'=> 'decimal:2'];

    public function parking(): BelongsTo
    {
        return $this->belongsTo(Parking::class, 'id_parking', 'id');
    }

    public function userClientTypes(): HasMany
    {
        return $this->hasMany(UserClientType::class, 'id_client_type', 'id');
    }
}
