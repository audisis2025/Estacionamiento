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
        Schema::create('plans', function (Blueprint $table) 
        {
            $table->id();
            $table->string('type', 20);
            $table->string('name', 60);
            $table->decimal('price', 10, 2)->default(0);
            $table->unsignedSmallInteger('duration_days');
            $table->string('description', 255);

            $table->unique(['type', 'name']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
