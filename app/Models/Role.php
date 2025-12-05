<?php
/*
* Nombre de la clase         : Role.php
* Descripción de la clase    : Modelo Eloquent para la tabla 'roles', que define los diferentes roles de usuario 
                               en el sistema.
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model 
{
    protected $table = 'roles';

    public $timestamps = false;

    protected $fillable = ['name'];

    public function users(): HasMany
    {
        return $this->hasMany(
            User::class, 
            'id_role', 
            'id'
        );
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
