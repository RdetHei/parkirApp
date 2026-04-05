<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_anpr_scans', function (Blueprint $table) {
            $table->id();
            $table->string('plat_nomor', 20);
            $table->float('confidence');
            $table->string('image_path')->nullable();
            $table->dateTime('scan_time');
            $table->json('json_response')->nullable(); // Bounding boxes, etc.
            $table->unsignedBigInteger('id_parkir')->nullable(); // Link to tb_transaksi
            $table->timestamps();

            $table->foreign('id_parkir')->references('id_parkir')->on('tb_transaksi')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_anpr_scans');
    }
};
