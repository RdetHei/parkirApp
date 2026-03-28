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
        Schema::table('tb_log_aktivitas', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->after('id_user');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('model_type')->nullable()->after('aktivitas');
            $table->unsignedBigInteger('model_id')->nullable()->after('model_type');
            $table->json('metadata')->nullable()->after('model_id');
            $table->string('tipe_aktivitas')->nullable()->after('aktivitas'); // e.g., 'transaksi', 'slot', 'auth'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_log_aktivitas', function (Blueprint $table) {
            $table->dropColumn(['ip_address', 'user_agent', 'model_type', 'model_id', 'metadata', 'tipe_aktivitas']);
        });
    }
};
