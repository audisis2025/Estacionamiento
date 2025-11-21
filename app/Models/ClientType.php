<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientType extends Model
{
    protected $table = 'client_types';

    public $timestamps = false;

    protected $fillable = [
        'typename',
        'discount_type',
        'amount',
        'id_parking',
    ];

    protected $casts = [
        'discount_type' => 'integer',
        'amount'        => 'decimal:2',
    ];

    public function parking(): BelongsTo
    {
        return $this->belongsTo(Parking::class, 'id_parking', 'id');
    }

    public function userClientTypes()
    {
        return $this->hasMany(UserClientType::class, 'id_client_type', 'id');
    }
}
