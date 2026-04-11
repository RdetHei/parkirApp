<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_kas_shift', function (Blueprint $table) {
            $table->id('id_kas_shift');
            $table->foreignId('opened_by')->constrained('tb_user', 'id')->cascadeOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('tb_user', 'id')->nullOnDelete();
            $table->foreignId('id_area')->nullable()->constrained('tb_area_parkir', 'id_area')->nullOnDelete();
            $table->dateTime('opened_at');
            $table->dateTime('closed_at')->nullable()->index('idx_tb_kas_shift_closed_at');
            $table->decimal('opening_cash_amount', 12, 0)->nullable()->comment('Modal awal kas (opsional)');
            $table->text('closing_note')->nullable();
            $table->timestamps();
        });

        Schema::table('tb_pembayaran', function (Blueprint $table) {
            $table->unsignedBigInteger('id_kas_shift')->nullable()->after('id_user');
            $table->decimal('cash_received', 12, 0)->nullable()->after('nominal');
            $table->decimal('cash_change', 12, 0)->nullable()->after('cash_received');

            $table->foreign('id_kas_shift', 'tb_pembayaran_id_kas_shift_foreign')
                ->references('id_kas_shift')
                ->on('tb_kas_shift')
                ->nullOnDelete();

            $table->index(['metode', 'status', 'waktu_pembayaran'], 'idx_tb_pembayaran_metode_status_waktu');
        });
    }

    public function down(): void
    {
        Schema::table('tb_pembayaran', function (Blueprint $table) {
            $table->dropForeign('tb_pembayaran_id_kas_shift_foreign');
            $table->dropIndex('idx_tb_pembayaran_metode_status_waktu');
            $table->dropColumn(['id_kas_shift', 'cash_received', 'cash_change']);
        });

        Schema::dropIfExists('tb_kas_shift');
    }
};
