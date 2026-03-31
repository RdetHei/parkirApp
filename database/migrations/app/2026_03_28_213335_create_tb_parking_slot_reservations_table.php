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
        Schema::create('tb_parking_slot_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parking_map_slot_id');
            $table->unsignedBigInteger('id_area');
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_kendaraan');
            $table->unsignedBigInteger('id_tarif');
            $table->dateTime('reserved_at');
            $table->dateTime('expires_at');
            $table->timestamps();

            $table->foreign('parking_map_slot_id')
                ->references('id')
                ->on('tb_parking_map_slots')
                ->onDelete('cascade');
            $table->foreign('id_area')
                ->references('id_area')
                ->on('tb_area_parkir')
                ->onDelete('cascade');
            $table->foreign('id_user')
                ->references('id')
                ->on('tb_user')
                ->onDelete('cascade');
            $table->foreign('id_kendaraan')
                ->references('id_kendaraan')
                ->on('tb_kendaraan')
                ->onDelete('cascade');
            $table->foreign('id_tarif')
                ->references('id_tarif')
                ->on('tb_tarif')
                ->onDelete('cascade');

            $table->index(['parking_map_slot_id', 'expires_at'], 'idx_resv_slot_expires');
            $table->index(['id_area', 'expires_at'], 'idx_resv_area_expires');
            $table->index(['id_user', 'expires_at'], 'idx_resv_user_expires');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_parking_slot_reservations');
    }
};
