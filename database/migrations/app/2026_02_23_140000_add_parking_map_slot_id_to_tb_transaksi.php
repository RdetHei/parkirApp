<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->unsignedBigInteger('parking_map_slot_id')->nullable()->after('id_area');
            $table->foreign('parking_map_slot_id')
                ->references('id')
                ->on('tb_parking_map_slots')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropForeign(['parking_map_slot_id']);
            $table->dropColumn('parking_map_slot_id');
        });
    }
};
