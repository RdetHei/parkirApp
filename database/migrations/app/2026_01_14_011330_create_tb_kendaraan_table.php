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
    Schema::create('tb_kendaraan', function (Blueprint $table) {
        $table->id('id_kendaraan');
        $table->string('plat_nomor', 15);
        $table->string('jenis_kendaraan', 20);
        $table->string('warna', 20);
        $table->string('pemilik', 100);
        $table->unsignedBigInteger('id_user');
        $table->timestamps();

        // Foreign Key
        $table->foreign('id_user')->references('id')->on('tb_user')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_kendaraan');
    }
};
