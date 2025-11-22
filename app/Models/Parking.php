<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'price_flat',
    ];

    protected $casts = [
        'latitude_coordinate' => 'float',
        'longitude_coordinate' => 'float',
        'type' => 'integer',
        'price' => 'float',
        'price_flat' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
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
        return $this->hasMany(Schedule::class, 'id_parking', 'id')->orderBy('id_day');
    }
    public function qrReaders()
    {
        return $this->hasMany(QrReader::class, 'id_parking');
    }
    public function clientTypes()
    {
        return $this->hasMany(ClientType::class, 'id_parking', 'id');
    }

    public function userClientTypes()
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
            \App\Models\Transaction::class,
            \App\Models\QrReader::class,
            'id_parking',     // FK en qr_readers hacia parkings
            'id_qr_reader',   // FK en transactions hacia qr_readers
            'id',             // PK en parkings
            'id'              // PK en qr_readers
        );
    }
}
