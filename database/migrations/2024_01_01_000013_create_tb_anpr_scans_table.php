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
            $table->double('confidence');
            $table->string('image_path')->nullable();
            $table->dateTime('scan_time');
            $table->json('json_response')->nullable();
            $table->foreignId('id_parkir')->nullable()->constrained('tb_transaksi', 'id_parkir')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_anpr_scans');
    }
};
