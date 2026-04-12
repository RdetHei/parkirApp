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
        Schema::create('parking_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_kendaraan')->constrained('tb_kendaraan', 'id_kendaraan')->onDelete('cascade');
            $table->timestamp('checkin_time');
            $table->timestamp('checkout_time')->nullable();
            $table->enum('gate_type', ['masuk', 'keluar']);
            $table->decimal('tariff_amount', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_logs');
    }
};
