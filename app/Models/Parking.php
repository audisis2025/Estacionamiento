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
    ];

    protected $casts = [
        'latitude_coordinate'  => 'float',
        'longitude_coordinate' => 'float',
        'type'                 => 'integer',
        'price'                => 'float',
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
        return $this->hasMany(\App\Models\QrReader::class, 'id_parking');
    }
}
