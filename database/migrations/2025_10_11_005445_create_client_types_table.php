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
        Schema::create('client_types', function (Blueprint $table) {
            $table->increments('id');                         // int auto_increment
            $table->string('typename', 50);                   // not null
            $table->boolean('discount_type');                 // not null
            $table->float('amount');                          // not null
            $table->unsignedInteger('id_parking')->nullable();

            $table->foreign('id_parking')->references('id')->on('parkings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_types');
    }
};
