<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

class Role extends Model 
{
    protected $table = 'roles';

    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_role', 'id');
    }

    public function isAdmin(): bool
    {
        return $this->id === 1;
    }

    public function isParkingAdmin(): bool
    {
        return $this->id === 2;
    }

    public function isUser(): bool
    {
        return $this->id === 3;
    }
}
