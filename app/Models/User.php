<?php
/*
* Nombre de la clase         : User.php
* Descripción de la clase    : Modelo principal para la gestión de usuarios, incluyendo autenticación, 
                               roles y planes de suscripción.
* Fecha de creación          : 02/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 02/11/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0 
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento : 
* Responsable                : 
* Revisor                    : 
*/
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasApiTokens;

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
        'notification_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'end_date' => 'datetime',
            'amount' => 'decimal:2',
            'password' => 'hashed'
        ];
    }

    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr(
                $word, 
                0, 
                1
            ))
            ->implode('');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'id_plan');
    }

    public function hasActivePlan(): bool
    {
        if (! $this->id_plan) 
        {
            return false;
        }

        if ((int) $this->id_plan === 4) 
        {
            return true;
        }

        if (! $this->end_date) 
        {
            return false;
        }

        return now()->lessThanOrEqualTo($this->end_date);
    }

    public function parking(): HasOne
    {
        return $this->hasOne(
            Parking::class, 
            'id_user', 
            'id'
        );
    }

    public function role(): BelongsTo
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

    public function userClientTypes(): HasMany
    {
        return $this->hasMany(
            UserClientType::class, 
            'id_user', 
            'id'
        );
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'id_user');
    }

    public function hasEnoughBalance(int|float $amount): bool
    {
        return (float)$this->amount >= (float)$amount;
    }
    
    public function activeUserClientTypeForParking(int $parkingId): ?UserClientType
    {
        return $this->userClientTypes()
            ->where('approval', 1)
            ->whereDate(
                'expiration_date', 
                '>=', 
                now()->toDateString()
            )
            ->whereHas('clientType', fn($q) => $q->where('id_parking', $parkingId))
            ->latest('id')
            ->first();
    }
}
