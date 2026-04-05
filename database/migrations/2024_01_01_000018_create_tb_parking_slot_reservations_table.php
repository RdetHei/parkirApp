<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_parking_slot_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_map_slot_id')->constrained('tb_parking_map_slots', 'id')->cascadeOnDelete();
            $table->foreignId('id_area')->constrained('tb_area_parkir', 'id_area')->cascadeOnDelete();
            $table->foreignId('id_user')->constrained('tb_user', 'id')->cascadeOnDelete();
            $table->foreignId('id_kendaraan')->constrained('tb_kendaraan', 'id_kendaraan')->cascadeOnDelete();
            $table->foreignId('id_tarif')->constrained('tb_tarif', 'id_tarif')->cascadeOnDelete();
            $table->dateTime('reserved_at');
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->index(['parking_map_slot_id', 'expires_at'], 'idx_resv_slot_expires');
            $table->index(['id_area', 'expires_at'], 'idx_resv_area_expires');
            $table->index(['id_user', 'expires_at'], 'idx_resv_user_expires');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_parking_slot_reservations');
    }
};
