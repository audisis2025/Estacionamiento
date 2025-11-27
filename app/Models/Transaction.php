<?php
/*
* Nombre de la clase         : Transaction.php
* Descripción de la clase    : Modelo Eloquent para la tabla 'transactions', que registra las entradas y 
                               salidas de vehículos y el monto cobrado.
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $table = 'transactions';

    public $timestamps = false;

    protected $fillable = [
        'amount',
        'entry_date',
        'departure_date',
        'id_qr_reader',
        'id_user',
        'billing_mode'
    ];

    protected $casts = [
        'amount' => 'float',
        'entry_date' => 'datetime',
        'departure_date' => 'datetime',
        'billing_mode' => 'string'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function qrReader(): BelongsTo
    {
        return $this->belongsTo(QrReader::class, 'id_qr_reader');
    }

    public function isOpen(): bool
    {
        return is_null($this->departure_date);
    }

    public function closeWithAmount(int $amount): void
    {
        $this->update(['amount'=> $amount,'departure_date' => now()]);
    }

    public function scopeOpenForUser(Builder $q, int $userId): Builder
    {
        return $q->where('id_user', $userId)->whereNull('departure_date');
    }
}
