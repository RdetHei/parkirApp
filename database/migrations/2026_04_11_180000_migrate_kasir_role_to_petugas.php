<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tb_user')->where('role', 'kasir')->update(['role' => 'petugas']);
    }

    public function down(): void
    {
        // Tidak bisa mengembalikan mana yang awalnya kasir
    }
};
