<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    protected $table = 'transactions';

    // Tu tabla no tiene created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'amount',
        'entry_date',
        'departure_date',
        'id_qr_reader',
        'id_user',
    ];

    protected $casts = [
        'amount'         => 'integer',
        'entry_date'     => 'datetime',
        'departure_date' => 'datetime',
    ];

    /* ---------------------------
     | Relaciones
     |----------------------------*/
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user');
    }

    public function qrReader()
    {
        return $this->belongsTo(\App\Models\QrReader::class, 'id_qr_reader');
    }

    /* ---------------------------
     | Helpers / Scopes
     |----------------------------*/
    public function isOpen(): bool
    {
        return is_null($this->departure_date);
    }

    public function closeWithAmount(int $amount): void
    {
        $this->update([
            'amount'         => $amount,
            'departure_date' => now(),
        ]);
    }

    public function scopeOpenForUser(Builder $q, int $userId): Builder
    {
        return $q->where('id_user', $userId)->whereNull('departure_date');
    }
}
