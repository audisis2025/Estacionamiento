<?php
/*
* Nombre de la clase         : 2025_10_11_005330_create_qr_readers_table.php
* Descripción de la clase    : Migración para crear la tabla de lectores de QR.
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
        Schema::create('qr_readers', function (Blueprint $table) 
        {
            $table->id();
            $table->string('serial_number', 20);
            $table->integer('sense');
            $table->unsignedBigInteger('id_parking')->nullable();

            $table->foreign('id_parking')->references('id')->on('parkings');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_readers');
    }
};
