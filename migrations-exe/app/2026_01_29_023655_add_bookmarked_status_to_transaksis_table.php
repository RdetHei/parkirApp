<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Needed for raw SQL

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            // Change the 'status' ENUM to include 'bookmarked'
            // This is how Laravel handles changing ENUMs gracefully
            $table->enum('status', ['masuk', 'keluar', 'bookmarked'])->change();
            
            // Add bookmarked_at column for the 10-minute timer
            $table->dateTime('bookmarked_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            // Remove bookmarked_at column
            $table->dropColumn('bookmarked_at');

            // Revert 'status' ENUM
            // Note: Reverting ENUM is tricky. A simple change() might fail if there's 'bookmarked' data.
            // This is a common challenge with ENUMs. For simplicity, we'll revert to original.
            $table->enum('status', ['masuk', 'keluar'])->change();
        });
    }
};