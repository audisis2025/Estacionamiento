<?php
/*
* Nombre de la clase         : 2025_10_11_005009_create_days_table.php
* Descripción de la clase    : Migración para crear la tabla de días.
* Fecha de creación          : 11/10/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 11/10/2025
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
        Schema::create('days', function (Blueprint $table) 
        {
            $table->id();
            $table->string('name', 9);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('days');
    }
};
