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
        Schema::create('schedules', function (Blueprint $table) {
            $table->increments('id');                       // int auto_increment
            $table->time('opening_time');                   // not null
            $table->time('closing_time');                   // not null
            $table->unsignedInteger('id_day')->nullable();
            $table->unsignedInteger('id_parking')->nullable();

            $table->foreign('id_day')->references('id')->on('days');
            $table->foreign('id_parking')->references('id')->on('parkings');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
