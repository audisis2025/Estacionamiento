<?php
/*
* Nombre de la clase         : 2025_11_22_140824_add_billing_mode_to_transactions_table.php
* Descripción de la clase    : Migración para agregar la columna de modo de facturación a la tabla de transacciones.
* Fecha de creación          : 22/11/2025
* Elaboró                    : Elian Pérez
* Fecha de liberación        : 22/11/2025
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
        Schema::table('transactions', function (Blueprint $table) 
        {
            $table->enum('billing_mode', ['hour', 'flat'])
                ->nullable()
                ->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) 
        {
            $table->dropColumn('billing_mode');
        });
    }
};