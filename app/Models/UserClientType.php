<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserClientType extends Model
{
    protected $table = 'user_client_types';
    public $timestamps = false;

    protected $fillable = [
        'approval',
        'expiration_date', 
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

    public function parking()
    {
        return $this->clientType?->parking();
    }

    public function scopePending($q)
    {
        return $q->where('approval', 0);
    }
    public function scopeApproved($q)
    {
        return $q->where('approval', 1);
    }
}
