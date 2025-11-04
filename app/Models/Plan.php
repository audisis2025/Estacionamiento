<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    public $timestamps = false;
    
    protected $fillable = [
        'name',
        'price',
        'duration_days',
        'description',
        'type',
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'duration_days' => 'integer',
    ];
    
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'id_plan', 'id');
    }

    public function scopeParking($q)
    {
        return $q->where('type', 'parking');
    }

}
