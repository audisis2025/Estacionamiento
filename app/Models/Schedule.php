<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $table = 'schedules';
    public $timestamps = false;

    protected $fillable = [
        'opening_time',
        'closing_time',
        'id_day',
        'id_parking',
    ];

    public function parking(): BelongsTo
    {
        return $this->belongsTo(Parking::class, 'id_parking', 'id');
    }

    public function day(): BelongsTo
    {
        return $this->belongsTo(Day::class, 'id_day', 'id');
    }
}
