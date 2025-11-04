<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use HasFactory;

class Role extends Model 
{
    /**
     * Nombre de la tabla (opcional si sigue convención)
     */
    protected $table = 'roles';

    /**
     * No necesitas timestamps si tu tabla no los tiene
     */
    public $timestamps = false;

    /**
     * Atributos que pueden asignarse masivamente
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Relación: un rol tiene muchos usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class, 'id_role', 'id');
    }

    /**
     * Helpers de acceso rápido
     */
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
