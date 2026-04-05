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
            $table->string('code', 30)->unique('tb_parking_map_slots_parking_map_id_code_unique');
            $table->unsignedInteger('x');
            $table->unsignedInteger('y');
            $table->unsignedInteger('width');
            $table->unsignedInteger('height');
            $table->foreignId('area_parkir_id')->constrained('tb_area_parkir', 'id_area')->cascadeOnDelete();
            $table->foreignId('camera_id')->nullable()->constrained('tb_kamera', 'id')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_parking_map_slots');
    }
};
