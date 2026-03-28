<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $candidates = DB::table('tb_parking_map_slots')
            ->select('parking_map_id', DB::raw('MIN(area_parkir_id) as area_id'), DB::raw('MAX(area_parkir_id) as area_id_max'))
            ->whereNotNull('area_parkir_id')
            ->groupBy('parking_map_id')
            ->havingRaw('MIN(area_parkir_id) = MAX(area_parkir_id)')
            ->get();

        foreach ($candidates as $c) {
            $mapId = (int) $c->parking_map_id;
            $areaId = (int) $c->area_id;

            $map = DB::table('tb_parking_maps')->where('id', $mapId)->first();
            if (!$map || $map->area_parkir_id) {
                continue;
            }

            $areaAlreadyUsed = DB::table('tb_parking_maps')->where('area_parkir_id', $areaId)->exists();
            if ($areaAlreadyUsed) {
                continue;
            }

            DB::table('tb_parking_maps')->where('id', $mapId)->update(['area_parkir_id' => $areaId]);
        }

        DB::statement("
            UPDATE tb_parking_map_slots s
            JOIN tb_parking_maps pm ON pm.id = s.parking_map_id
            SET s.area_parkir_id = pm.area_parkir_id
            WHERE s.area_parkir_id IS NULL AND pm.area_parkir_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
