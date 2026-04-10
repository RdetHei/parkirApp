<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_parking_map_slots', function (Blueprint $table) {
            // Drop the existing unique constraint
            $table->dropUnique('tb_parking_map_slots_parking_map_id_code_unique');
            
            // Add a new unique constraint on (area_parkir_id, code)
            $table->unique(['area_parkir_id', 'code'], 'tb_parking_map_slots_area_code_unique');
        });
    }

    public function down(): void
    {
        Schema::table('tb_parking_map_slots', function (Blueprint $table) {
            $table->dropUnique('tb_parking_map_slots_area_code_unique');
            $table->unique('code', 'tb_parking_map_slots_parking_map_id_code_unique');
        });
    }
};
