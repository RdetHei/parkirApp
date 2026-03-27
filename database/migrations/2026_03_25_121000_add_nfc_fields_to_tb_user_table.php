<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            // UID kartu (opsional), agar admin bisa lihat kartu mana yang terpasang.
            $table->string('nfc_uid', 128)->nullable()->unique();

            // Untuk integrasi NFC (tetap sinkron dengan kolom existing `saldo`).
            $table->decimal('balance', 15, 2)->default(0)->after('saldo');

            // Foto user untuk popup saat tap/scan.
            $table->string('photo', 255)->nullable()->after('balance');
        });

        // Sinkronkan `balance` dengan `saldo` untuk data user lama.
        DB::table('tb_user')->update([
            'balance' => DB::raw('saldo'),
        ]);
    }

    public function down(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->dropColumn(['nfc_uid', 'balance', 'photo']);
        });
    }
};

