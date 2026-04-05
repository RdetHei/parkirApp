<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_parking_map_cameras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('area_parkir_id')->constrained('tb_area_parkir', 'id_area')->cascadeOnDelete();
            $table->foreignId('camera_id')->unique('tb_parking_map_cameras_parking_map_id_camera_id_unique')->constrained('tb_kamera', 'id')->cascadeOnDelete();
            $table->unsignedInteger('x');
            $table->unsignedInteger('y');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_parking_map_cameras');
    }
};
