<?php
/*
* Nombre de la clase         : 2025_10_11_004837_modify_users_table_add_parking_fields.php
* Descripción de la clase    : Migración para modificar la tabla de usuarios y agregar campos relacionados 
                               con el estacionamiento.
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
        Schema::table('users', function (Blueprint $table) 
        {
            $table->dateTime('end_date')->nullable()->after('password');
            $table->decimal(
                'amount', 
                10, 
                2
            )->nullable()->default(0)->after('end_date');
            $table->string('phone_number', 10)->unique()->after('amount');
            $table->string('notification_token', 255)->nullable();
            $table->unsignedBigInteger('id_plan')->nullable()->after('phone_number');
            $table->unsignedBigInteger('id_role')->nullable()->after('id_plan');

            $table->foreign('id_plan')->references('id')->on('plans');
            $table->foreign('id_role')->references('id')->on('roles');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) 
        {
            $table->dropForeign(['id_plan']);
            $table->dropForeign(['id_role']);
            $table->dropColumn([
                'end_date',
                'amount',
                'phone_number',
                'id_plan',
                'id_role'
            ]);
        });
    }
};
