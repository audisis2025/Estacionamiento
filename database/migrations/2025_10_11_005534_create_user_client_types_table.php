<?php
/*
* Nombre de la clase         : 2025_10_11_005534_create_user_client_types_table.php
* Descripción de la clase    : Migración para crear la tabla de tipos de clientes de usuarios.
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
        Schema::create('user_client_types', function (Blueprint $table) 
        {
            $table->id();
            $table->integer('approval');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->unsignedBigInteger('id_client_type')->nullable();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_client_type')->references('id')->on('client_types');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_client_types');
    }
};
