<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Day extends Model
{
    protected $table = 'days';
    public $timestamps = false;

    protected $fillable = ['name'];

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'id_day', 'id');
    }
}
