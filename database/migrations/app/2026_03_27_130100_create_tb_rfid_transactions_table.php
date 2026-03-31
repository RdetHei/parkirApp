<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_rfid_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('tb_user')->cascadeOnDelete();

            // IN / OUT / PAYMENT
            $table->string('type', 20);

            // Amount is only relevant for OUT/PAYMENT; nullable for IN.
            $table->decimal('amount', 15, 2)->nullable();

            // Spec: only created_at
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_rfid_transactions');
    }
};

