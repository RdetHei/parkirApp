<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->unsignedBigInteger('id_parkir');
            $table->string('order_id', 64)->nullable();
            $table->string('transaction_id', 64)->nullable();
            $table->string('payment_type', 32)->nullable();
            $table->decimal('nominal', 10, 0);
            $table->string('metode', 50)->default('manual');
            $table->string('status', 50)->default('pending');
            $table->text('keterangan')->nullable();
            $table->foreignId('id_user')->nullable()->constrained('tb_user', 'id')->nullOnDelete();
            $table->dateTime('waktu_pembayaran')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('id_parkir', 'tb_pembayaran_id_parkir_foreign');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_pembayaran');
    }
};
