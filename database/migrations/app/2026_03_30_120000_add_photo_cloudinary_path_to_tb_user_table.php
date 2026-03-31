<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('tb_user', 'photo_cloudinary_path')) {
            return;
        }

        Schema::table('tb_user', function (Blueprint $table) {
            $table->string('photo_cloudinary_path', 512)->nullable()->after('photo');
        });
    }

    public function down(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            if (Schema::hasColumn('tb_user', 'photo_cloudinary_path')) {
                $table->dropColumn('photo_cloudinary_path');
            }
        });
    }
};
