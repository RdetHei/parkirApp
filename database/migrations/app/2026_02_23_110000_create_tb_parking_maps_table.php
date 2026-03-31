<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_parking_maps', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 50)->unique(); // mis: floor1, floor2, outside
            $table->string('image_path', 255);    // mis: images/floor1.png
            $table->unsignedInteger('width');     // pixel width image asli
            $table->unsignedInteger('height');    // pixel height image asli
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_parking_maps');
    }
};

