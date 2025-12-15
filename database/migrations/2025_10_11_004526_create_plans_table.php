<?php
/*
* Nombre de la clase         : 2025_10_11_004526_create_plans_table.php
* Descripción de la clase    : Migración para crear la tabla de planes.
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
        Schema::create('plans', function (Blueprint $table) 
        {
            $table->id();
            $table->string('type', 20);
            $table->string('name', 60);
            $table->decimal(
                'price', 
                10, 
                2
            )->default(0);
            $table->unsignedSmallInteger('duration_days');
            $table->string('description', 255);

            $table->unique(['type', 'name']);
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
