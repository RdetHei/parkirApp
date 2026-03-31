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
        Schema::table('tb_transaksi', function (Blueprint $table) {
            // Ubah status_pembayaran enum ke status yang consistent dengan Pembayaran
            if (Schema::hasColumn('tb_transaksi', 'status_pembayaran')) {
                $table->dropColumn('status_pembayaran');
            }

            $table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal'])->default('pending')->after('status');

            if (!Schema::hasColumn('tb_transaksi', 'id_pembayaran')) {
                $table->unsignedBigInteger('id_pembayaran')->nullable()->after('status_pembayaran');
                $table->foreign('id_pembayaran')->references('id_pembayaran')->on('tb_pembayaran')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropForeign(['id_pembayaran']);
            $table->dropColumn('id_pembayaran');
            $table->dropColumn('status_pembayaran');
        });
    }
};
