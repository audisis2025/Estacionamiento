<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tus columnas
            $table->dateTime('end_date')->nullable()->after('password');
            $table->float('amount')->default(0)->after('end_date');
            $table->string('phone_number', 10)->unique()->after('amount');
            $table->string('notification_token', 255)->nullable();
            // Foreign keys (tipos compatibles con INT en roles/plans)
            $table->unsignedBigInteger('id_plan')->nullable()->after('phone_number');
            $table->unsignedInteger('id_role')->nullable()->after('id_plan');

            $table->foreign('id_plan')->references('id')->on('plans');
            $table->foreign('id_role')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_plan']);
            $table->dropForeign(['id_role']);
            $table->dropColumn(['end_date','amount','phone_number','id_plan','id_role']);
        });
    }
};
