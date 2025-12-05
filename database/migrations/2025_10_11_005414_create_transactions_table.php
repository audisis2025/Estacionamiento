<?php
/*
* Nombre de la clase         : 2025_10_11_005414_create_transactions_table.php
* Descripción de la clase    : Migración para crear la tabla de transacciones.
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
        Schema::create('transactions', function (Blueprint $table) 
        {
            $table->id();
            $table->decimal(
                'amount', 
                10, 
                2
            )->nullable();
            $table->dateTime('entry_date');
            $table->dateTime('departure_date')->nullable();
            $table->unsignedBigInteger('id_qr_reader')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();

            $table->foreign('id_qr_reader')->references('id')->on('qr_readers');
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
