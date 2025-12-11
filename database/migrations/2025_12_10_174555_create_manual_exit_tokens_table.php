<?php
/*
* Nombre de la clase         : 2025_12_10_174555_create_manual_exit_tokens_table.php
* Descripción de la clase    : Migración para crear la tabla manual_exit_tokens
*                              que almacena los tokens de salida manual.
* Fecha de creación          : 10/12/2025
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
        Schema::create('manual_exit_tokens', function (Blueprint $table) 
        {
            $table->id();
            $table->string('token', 64)->unique();
            $table->unsignedBigInteger('transaction_id');
            $table->unsignedBigInteger('id_parking');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->foreign('transaction_id')
                ->references('id')->on('transactions')
                ->onDelete('cascade');

            $table->foreign('id_parking')
                ->references('id')->on('parkings')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_exit_tokens');
    }
};
