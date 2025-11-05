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
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');                           // int auto_increment
            $table->integer('amount')->nullable();             
            $table->dateTime('entry_date');                     // not null
            $table->dateTime('departure_date')->nullable();     // null
            $table->unsignedInteger('id_qr_reader')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();

            $table->foreign('id_qr_reader')->references('id')->on('qr_readers');
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
