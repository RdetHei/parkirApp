<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ensure balance has the latest values from saldo if balance is 0 or null
        DB::table('tb_user')->where(function($q) {
            $q->whereNull('balance')->orWhere('balance', 0);
        })->update(['balance' => DB::raw('saldo')]);

        // 2. Drop the redundant 'saldo' column
        Schema::table('tb_user', function (Blueprint $table) {
            if (Schema::hasColumn('tb_user', 'saldo')) {
                $table->dropColumn('saldo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->decimal('saldo', 15, 2)->default(0)->after('role');
        });

        DB::table('tb_user')->update(['saldo' => DB::raw('balance')]);
    }
};
