<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_parking_map_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_map_id')->constrained('tb_parking_maps')->cascadeOnDelete();
            $table->string('code', 30);
            $table->unsignedInteger('x');
            $table->unsignedInteger('y');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->unsignedBigInteger('area_parkir_id')->nullable();
            $table->unsignedBigInteger('camera_id')->nullable();
            $table->string('notes', 255)->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->unique(['parking_map_id', 'code']);
            $table->foreign('area_parkir_id')->references('id_area')->on('tb_area_parkir')->nullOnDelete();
            $table->foreign('camera_id')->references('id')->on('tb_kamera')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_parking_map_slots');
    }
};
