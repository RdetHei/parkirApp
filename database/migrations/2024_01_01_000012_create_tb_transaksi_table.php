<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->id('id_parkir');
            $table->foreignId('id_kendaraan')->nullable()->constrained('tb_kendaraan', 'id_kendaraan')->cascadeOnDelete();
            $table->dateTime('waktu_masuk')->nullable()->index('idx_tb_transaksi_waktu_masuk');
            $table->dateTime('waktu_keluar')->nullable()->index('idx_tb_transaksi_waktu_keluar');
            $table->foreignId('id_tarif')->nullable()->constrained('tb_tarif', 'id_tarif')->cascadeOnDelete();
            $table->integer('durasi_jam')->nullable();
            $table->decimal('biaya_total', 10, 0)->nullable();
            $table->enum('status', ['masuk', 'keluar', 'bookmarked'])->index('idx_tb_transaksi_status');
            $table->dateTime('bookmarked_at')->nullable();
            $table->string('catatan')->nullable();
            $table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->foreignId('id_pembayaran')->nullable()->constrained('tb_pembayaran', 'id_pembayaran')->nullOnDelete();
            $table->string('midtrans_order_id', 100)->nullable();
            $table->foreignId('id_user')->constrained('tb_user', 'id')->cascadeOnDelete();
            $table->foreignId('id_area')->constrained('tb_area_parkir', 'id_area')->cascadeOnDelete();
            $table->foreignId('parking_map_slot_id')->nullable()->constrained('tb_parking_map_slots', 'id')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_transaksi');
    }
};
