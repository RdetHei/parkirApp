<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop FK terlebih dahulu agar kolom bisa diubah
        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
        });

        $driver = DB::getDriverName();

        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            throw new RuntimeException("Migration ini hanya mendukung MySQL/MariaDB. Driver terdeteksi: {$driver}");
        }

        // Jadikan kolom opsional: kendaraan tidak wajib punya user, warna, dan pemilik
        DB::statement('ALTER TABLE tb_kendaraan MODIFY id_user BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE tb_kendaraan MODIFY warna VARCHAR(20) NULL');
        DB::statement('ALTER TABLE tb_kendaraan MODIFY pemilik VARCHAR(100) NULL');

        // Re-add FK dengan on delete set null
        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table
                ->foreign('id_user', 'tb_kendaraan_id_user_fk')
                ->references('id')
                ->on('tb_user')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Reverse (best-effort): kembalikan seperti semula (wajib + cascade)
        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table->dropForeign('tb_kendaraan_id_user_fk');
        });

        $driver = DB::getDriverName();

        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            throw new RuntimeException("Rollback migration ini hanya mendukung MySQL/MariaDB. Driver terdeteksi: {$driver}");
        }

        // NOTE: Jika ada data NULL, rollback akan gagal sampai data dibersihkan.
        DB::statement('ALTER TABLE tb_kendaraan MODIFY id_user BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE tb_kendaraan MODIFY warna VARCHAR(20) NOT NULL');
        DB::statement('ALTER TABLE tb_kendaraan MODIFY pemilik VARCHAR(100) NOT NULL');

        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table
                ->foreign('id_user')
                ->references('id')
                ->on('tb_user')
                ->onDelete('cascade');
        });
    }
};

