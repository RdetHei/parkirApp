<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_kamera', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('url', 500)->comment('URL stream IP Webcam, mis. http://localhost:8080/video');
            $table->string('tipe', 20)->default('scanner')->comment('scanner = scan plat di Catat Masuk, viewer = tampil di map interaktif');
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_kamera');
    }
};
