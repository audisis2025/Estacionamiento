<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Atributos que se pueden asignar masivamente
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'id_plan',
        'id_role',
        'end_date',
        'amount',
    ];

    /**
     * Atributos que deben ocultarse al serializar
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Atributos con casteo automático
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'end_date' => 'datetime',
            'amount' => 'integer',
            'password' => 'hashed',
        ];
    }

    /**
     * Relaciones
     */
    // public function plan()
    // {
    //     return $this->belongsTo(Plan::class, 'id_plan');
    // }

    // public function role()
    // {
    //     return $this->belongsTo(Role::class, 'id_role');
    // }

    /**
     * Iniciales del usuario
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
