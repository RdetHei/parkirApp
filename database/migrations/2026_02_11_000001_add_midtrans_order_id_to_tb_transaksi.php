<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menyimpan order_id Midtrans agar status pembayaran bisa disinkronkan saat user buka halaman success
     * (jika notifikasi server dari Midtrans tidak sampai, misal development di localhost).
     */
    public function up(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->string('midtrans_order_id', 100)->nullable()->after('id_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropColumn('midtrans_order_id');
        });
    }
};
