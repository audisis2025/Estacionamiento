<?php
/*
* Nombre de la clase         : QrReader.php
* Descripción de la clase    : Modelo Eloquent para la tabla 'qr_readers', que representa los dispositivos 
                               lectores de códigos QR en el estacionamiento.
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

class QrReader extends Model
{
    protected $table = 'qr_readers';
    public $timestamps = false;
    protected $fillable = [
        'serial_number', 
        'sense', 
        'id_parking'
    ];

    public function parking(): BelongsTo
    {
        return $this->belongsTo(Parking::class, 'id_parking');
    }
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'id_qr_reader');
    }
}
