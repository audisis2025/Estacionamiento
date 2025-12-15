<?php
/*
* Nombre de la clase         : 2025_10_11_005445_create_client_types_table.php
* Descripción de la clase    : Migración para crear la tabla de usuarios.
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
        Schema::create('client_types', function (Blueprint $table) 
        {
            $table->id();
            $table->string('type_name', 50);
            $table->boolean('discount_type');
            $table->decimal(
                'amount', 
                10, 
                2
            );
            $table->unsignedBigInteger('id_parking')->nullable();

            $table->foreign('id_parking')->references('id')->on('parkings');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_types');
    }
};
