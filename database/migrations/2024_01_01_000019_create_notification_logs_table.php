<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('tb_user', 'id')->nullOnDelete();
            $table->string('type', 32);
            $table->string('status', 16);
            $table->text('message')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type', 'created_at'], 'notification_logs_user_id_type_created_at_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
