<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_area_parkir', function (Blueprint $table) {
            $table->id('id_area');
            $table->string('nama_area', 50);
            $table->string('daerah')->nullable();
            $table->string('map_code', 50)->nullable();
            $table->string('map_image')->nullable();
            $table->unsignedInteger('map_width')->default(1000);
            $table->unsignedInteger('map_height')->default(800);
            $table->boolean('is_default_map')->default(false);
            $table->integer('kapasitas');
            $table->integer('terisi')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_area_parkir');
    }
};
