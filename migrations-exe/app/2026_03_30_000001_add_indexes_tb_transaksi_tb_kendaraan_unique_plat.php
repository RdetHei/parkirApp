<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->index('waktu_masuk', 'idx_tb_transaksi_waktu_masuk');
            $table->index('waktu_keluar', 'idx_tb_transaksi_waktu_keluar');
            $table->index('status', 'idx_tb_transaksi_status');
        });

        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table->unique('plat_nomor', 'uq_tb_kendaraan_plat_nomor');
        });
    }

    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropIndex('idx_tb_transaksi_waktu_masuk');
            $table->dropIndex('idx_tb_transaksi_waktu_keluar');
            $table->dropIndex('idx_tb_transaksi_status');
        });

        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table->dropUnique('uq_tb_kendaraan_plat_nomor');
        });
    }
};

