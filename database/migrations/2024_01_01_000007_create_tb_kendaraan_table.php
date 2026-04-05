<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_kendaraan', function (Blueprint $table) {
            $table->id('id_kendaraan');
            $table->string('plat_nomor', 15)->unique('uq_tb_kendaraan_plat_nomor');
            $table->string('jenis_kendaraan', 20);
            $table->string('warna', 20)->nullable();
            $table->string('pemilik', 100)->nullable();
            $table->foreignId('id_user')->nullable()->constrained('tb_user', 'id', 'tb_kendaraan_id_user_fk')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_kendaraan');
    }
};
