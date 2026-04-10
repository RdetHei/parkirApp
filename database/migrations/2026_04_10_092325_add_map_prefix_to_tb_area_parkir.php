<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_area_parkir', function (Blueprint $table) {
            $table->string('map_prefix', 10)->nullable()->after('nama_area');
        });
    }

    public function down(): void
    {
        Schema::table('tb_area_parkir', function (Blueprint $table) {
            $table->dropColumn('map_prefix');
        });
    }
};
