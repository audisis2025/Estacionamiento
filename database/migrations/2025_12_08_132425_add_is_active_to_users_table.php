<?php
/*
* Nombre de la clase         : 2025_12_08_000000_add_is_active_to_users_table.php
* Descripción de la clase    : Migración para agregar el campo is_active a la tabla users
*                              y permitir activar/desactivar cuentas de usuario.
* Fecha de creación          : 08/12/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 08/12/2025
* Autorizó                   : Angel Davila
* Versión                    : 1.0
* Fecha de mantenimiento     : 
* Folio de mantenimiento     : 
* Tipo de mantenimiento      : 
* Descripción del mantenimiento :
* Responsable                : 
* Revisor                    : 
*/
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) 
        {
            $table->boolean('is_active')
                ->default(true)
                ->after('id_role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) 
        {
            $table->dropColumn('is_active');
        });
    }
};