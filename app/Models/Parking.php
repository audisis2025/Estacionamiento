<?php
/*
* Nombre de la clase         : Parking.php
* Descripción de la clase    : Modelo Eloquent para la tabla 'parkings', que representa un estacionamiento y sus
                               relaciones.
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
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Parking extends Model
{
    public $timestamps = false;

    protected $table = 'parkings';

    protected $fillable = [
        'id_user',
        'name',
        'latitude_coordinate',
        'longitude_coordinate',
        'type',
        'price',
        'price_flat'
    ];

    protected $casts = [
        'latitude_coordinate' => 'float',
        'longitude_coordinate' => 'float',
        'type' => 'integer',
        'price' => 'float',
        'price_flat' => 'float'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class, 
            'id_user', 
            'id'
        );
    }

    public function getLatAttribute(): ?float
    {
        return $this->latitude_coordinate;
    }

    public function getLngAttribute(): ?float
    {
        return $this->longitude_coordinate;
    }
    public function schedules(): HasMany
    {
        return $this->hasMany(
            Schedule::class, 
            'id_parking', 
            'id'
            )->orderBy('id_day');
    }
    public function qrReaders(): HasMany
    {
        return $this->hasMany(QrReader::class, 'id_parking');
    }
    public function clientTypes(): HasMany
    {
        return $this->hasMany(
            ClientType::class, 
            'id_parking', 
            'id'
        );
    }

    public function userClientTypes(): HasManyThrough
    {
        return $this->hasManyThrough(
            UserClientType::class,
            ClientType::class,
            'id_parking',
            'id_client_type',
            'id',
            'id'
        );
    }
    public function transactions()
    {
        return $this->hasManyThrough(
            Transaction::class,
            QrReader::class,
            'id_parking',
            'id_qr_reader',
            'id',
            'id'
        );
    }
}