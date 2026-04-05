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
            $table->foreignId('user_id')->constrained('tb_user', 'id')->cascadeOnDelete();
            $table->string('type', 20);
            $table->decimal('amount', 15, 2)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at'], 'tb_rfid_transactions_user_id_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_rfid_transactions');
    }
};
