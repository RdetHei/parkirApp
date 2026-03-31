<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_kamera', function (Blueprint $table) {
            $table->string('tipe', 20)->default('scanner')->after('url')->comment('scanner = scan plat di Catat Masuk, viewer = tampil di map interaktif');
        });
    }

    public function down(): void
    {
        Schema::table('tb_kamera', function (Blueprint $table) {
            $table->dropColumn('tipe');
        });
    }
};
