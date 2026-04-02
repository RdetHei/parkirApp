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
            $table->dateTime('waktu_masuk')->nullable()->change();
            $table->unsignedBigInteger('id_kendaraan')->nullable()->change();
            $table->unsignedBigInteger('id_tarif')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dateTime('waktu_masuk')->nullable(false)->change();
            $table->unsignedBigInteger('id_kendaraan')->nullable(false)->change();
            $table->unsignedBigInteger('id_tarif')->nullable(false)->change();
        });
    }
};
