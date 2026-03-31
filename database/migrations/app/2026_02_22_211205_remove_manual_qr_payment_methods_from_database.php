<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menghapus data pembayaran dengan metode manual dan qr_scan.
     * Hanya menyisakan pembayaran dengan metode midtrans.
     */
    public function up(): void
    {
        // Hapus semua pembayaran dengan metode manual atau qr_scan
        DB::table('tb_pembayaran')
            ->whereIn('metode', ['manual', 'qr_scan'])
            ->delete();

        // Update transaksi yang terkait dengan pembayaran yang dihapus
        // Set status_pembayaran kembali ke 'pending' dan id_pembayaran ke null
        DB::table('tb_transaksi')
            ->whereIn('status_pembayaran', ['berhasil'])
            ->whereNotNull('id_pembayaran')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('tb_pembayaran')
                    ->whereColumn('tb_pembayaran.id_pembayaran', 'tb_transaksi.id_pembayaran');
            })
            ->update([
                'status_pembayaran' => 'pending',
                'id_pembayaran' => null,
            ]);
    }

    /**
     * Reverse the migrations.
     * Note: Tidak bisa mengembalikan data yang sudah dihapus.
     */
    public function down(): void
    {
        // Tidak bisa mengembalikan data yang sudah dihapus
        // Migration ini hanya untuk cleanup, tidak bisa di-rollback
    }
};
