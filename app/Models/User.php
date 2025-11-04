<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens;

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
        'id_role',
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
            'amount' => 'decimal:2',
            'password' => 'hashed',
        ];
    }

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

    public function plan()
    {
        return $this->belongsTo(\App\Models\Plan::class, 'id_plan');
    }

    public function hasActivePlan(): bool
    {
        if (!$this->id_plan || !$this->end_date) return false;
        return now()->lessThanOrEqualTo(\Carbon\Carbon::parse($this->end_date));
    }

    public function parking()
    {
        return $this->hasOne(Parking::class, 'id_user', 'id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role');
    }

    public function isAdmin(): bool
    {
        return (int) $this->id_role === 1;
    }

    public function isParkingAdmin(): bool
    {
        return (int) $this->id_role === 2;
    }

    public function isUser(): bool
    {
        return (int) $this->id_role === 3;
    }
}
