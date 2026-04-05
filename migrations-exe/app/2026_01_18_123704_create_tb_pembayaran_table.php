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
        Schema::create('tb_pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->unsignedBigInteger('id_parkir');
            $table->decimal('nominal', 10, 0);
            $table->enum('metode', ['manual', 'qr_scan'])->default('manual');
            $table->enum('status', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('id_user')->nullable(); // Petugas yang proses pembayaran
            $table->dateTime('waktu_pembayaran')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('id_parkir')->references('id_parkir')->on('tb_transaksi')->onDelete('cascade');
            $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pembayaran');
    }
};
