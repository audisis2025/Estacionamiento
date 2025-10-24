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
        Schema::create('user_client_types', function (Blueprint $table) {
            $table->increments('id');                          // int auto_increment
            $table->integer('approval');                       // not null
            $table->date('expiration_date')->nullable();
            $table->unsignedBigInteger('id_user')->nullable();
            $table->unsignedInteger('id_client_type')->nullable();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_client_type')->references('id')->on('client_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_client_types');
    }
};
