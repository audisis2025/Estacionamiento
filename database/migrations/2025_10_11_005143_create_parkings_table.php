<?php
/*
* Nombre de la clase         : 2025_10_11_005143_create_parkings_table.php
* Descripción de la clase    : Migración para crear la tabla de parkings.
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
        Schema::create('parkings', function (Blueprint $table) 
        {
            $table->id();
            $table->string('name', 30);
            $table->float('latitude_coordinate');
            $table->float('longitude_coordinate');
            $table->tinyInteger('type');
            $table->decimal(
                'price', 
                10, 
                2
            )->nullable();
            $table->float('price_flat')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();

            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parkings');
    }
};
