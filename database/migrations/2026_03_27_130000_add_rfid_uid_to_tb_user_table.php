<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $columns = Schema::getColumnListing('tb_user');

        Schema::table('tb_user', function (Blueprint $table) use ($columns) {
            // RFID/keyboard wedge sends UID number; stored as identifier only.
            if (! in_array('rfid_uid', $columns, true)) {
                $table->string('rfid_uid', 128)->nullable()->unique();
            }

            // Keep spec-compatible columns; only add if missing (don't alter if existing).
            if (! in_array('balance', $columns, true)) {
                $table->integer('balance')->default(0)->after('saldo');
            }

            if (! in_array('photo', $columns, true)) {
                $table->string('photo', 255)->nullable()->after('balance');
            }
        });

        // Sync balance with saldo for legacy users (if both columns exist).
        if (Schema::hasColumn('tb_user', 'balance') && Schema::hasColumn('tb_user', 'saldo')) {
            DB::table('tb_user')->update([
                'balance' => DB::raw('saldo'),
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('tb_user', 'rfid_uid')) {
            return;
        }

        Schema::table('tb_user', function (Blueprint $table) {
            $table->dropUnique(['rfid_uid']);
            $table->dropColumn('rfid_uid');
        });
    }
};

