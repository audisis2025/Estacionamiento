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
        Schema::create('parkings', function (Blueprint $table) {
            $table->increments('id');                           // int auto_increment
            $table->string('name', 30);                         // not null
            $table->float('latitude_coordinate');               // not null
            $table->float('longitude_coordinate');              // not null
            $table->tinyInteger('type');                       // not null
            $table->decimal('price', 10, 2)->nullable();  
            $table->float('price_flat')->nullable();                           // not null
            $table->unsignedBigInteger('id_user')->nullable();  // FK a users

            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parkings');
    }
};
