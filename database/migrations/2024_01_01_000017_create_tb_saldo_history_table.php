<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_saldo_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('tb_user', 'id')->cascadeOnDelete();
            $table->decimal('amount', 15, 2);
            $table->enum('type', ['topup', 'payment', 'refund']);
            $table->string('description')->nullable();
            $table->string('reference_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_saldo_history');
    }
};
