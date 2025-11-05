<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserClientType extends Model
{
    protected $table = 'user_client_types';
    public $timestamps = false;

    protected $fillable = [
        'approval',        // 0|1
        'expiration_date', // nullable date
        'id_user',
        'id_client_type',
    ];

    protected $casts = [
        'approval'        => 'integer',
        'expiration_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function clientType(): BelongsTo
    {
        return $this->belongsTo(ClientType::class, 'id_client_type', 'id');
    }

    // Atajo: $request->parking devuelve el parking del tipo elegido
    public function parking()
    {
        return $this->clientType?->parking();
    }

    /* Scopes de conveniencia */
    public function scopePending($q)
    {
        return $q->where('approval', 0);
    }
    public function scopeApproved($q)
    {
        return $q->where('approval', 1);
    }
}
