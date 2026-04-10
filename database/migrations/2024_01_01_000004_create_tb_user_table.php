<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_user', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('user');
            $table->decimal('saldo', 15, 2)->default(0.00);
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->string('photo')->nullable();
            $table->string('photo_cloudinary_path', 512)->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->string('rfid_uid', 128)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_user');
    }
};
