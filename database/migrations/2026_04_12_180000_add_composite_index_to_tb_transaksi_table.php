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
            // Index komposit untuk mempercepat pencarian transaksi aktif berdasarkan user
            // Query: where('id_user', $user->id)->where('status', 'masuk')->latest('waktu_masuk')
            $table->index(['id_user', 'status', 'waktu_masuk'], 'idx_transaksi_user_status_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropIndex('idx_transaksi_user_status_time');
        });
    }
};
