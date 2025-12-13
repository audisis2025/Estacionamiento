<?php
/*
* Nombre de la clase         : 2025_10_11_004510_create_roles_table.php
* Descripción de la clase    : Migración para crear la tabla de roles.
* Fecha de creación          : 
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 
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
        Schema::create('roles', function (Blueprint $table) 
        {
            $table->id();
            $table->string('name', 20);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
