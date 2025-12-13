<?php
/*
* Nombre de la clase         : 2025_10_11_005226_create_schedules_table.php
* Descripción de la clase    : Migración para crear la tabla de horarios.
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
        Schema::create('schedules', function (Blueprint $table) 
        {
            $table->id();
            $table->time('opening_time');
            $table->time('closing_time');
            $table->unsignedBigInteger('id_day')->nullable();
            $table->unsignedBigInteger('id_parking')->nullable();

            $table->foreign('id_day')->references('id')->on('days');
            $table->foreign('id_parking')->references('id')->on('parkings');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
