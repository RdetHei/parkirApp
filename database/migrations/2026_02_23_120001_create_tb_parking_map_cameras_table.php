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
            $table->foreignId('parking_map_id')->constrained('tb_parking_maps')->cascadeOnDelete();
            $table->foreignId('camera_id')->constrained('tb_kamera')->cascadeOnDelete();
            $table->unsignedInteger('x');
            $table->unsignedInteger('y');
            $table->timestamps();

            $table->unique(['parking_map_id', 'camera_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_parking_map_cameras');
    }
};
