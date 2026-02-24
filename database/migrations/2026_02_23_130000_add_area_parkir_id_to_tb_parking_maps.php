<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_parking_maps', function (Blueprint $table) {
            $table->unsignedBigInteger('area_parkir_id')->nullable()->unique()->after('id');
            $table->foreign('area_parkir_id')
                ->references('id_area')
                ->on('tb_area_parkir')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('tb_parking_maps', function (Blueprint $table) {
            $table->dropForeign(['area_parkir_id']);
            $table->dropColumn('area_parkir_id');
        });
    }
};
