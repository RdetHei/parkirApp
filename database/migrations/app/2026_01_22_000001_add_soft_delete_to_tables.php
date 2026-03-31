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
        // Add soft delete to tb_user
        if (Schema::hasTable('tb_user') && !Schema::hasColumn('tb_user', 'deleted_at')) {
            Schema::table('tb_user', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft delete to tb_kendaraan
        if (Schema::hasTable('tb_kendaraan') && !Schema::hasColumn('tb_kendaraan', 'deleted_at')) {
            Schema::table('tb_kendaraan', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft delete to tb_transaksi
        if (Schema::hasTable('tb_transaksi') && !Schema::hasColumn('tb_transaksi', 'deleted_at')) {
            Schema::table('tb_transaksi', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft delete to tb_pembayaran
        if (Schema::hasTable('tb_pembayaran') && !Schema::hasColumn('tb_pembayaran', 'deleted_at')) {
            Schema::table('tb_pembayaran', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('tb_kendaraan', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('tb_pembayaran', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
