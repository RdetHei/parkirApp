<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom peta ke tb_area_parkir
        Schema::table('tb_area_parkir', function (Blueprint $table) {
            if (!Schema::hasColumn('tb_area_parkir', 'map_code')) {
                $table->string('map_code', 50)->nullable()->after('nama_area');
            }
            if (!Schema::hasColumn('tb_area_parkir', 'map_image')) {
                $table->string('map_image', 255)->nullable()->after('map_code');
            }
            if (!Schema::hasColumn('tb_area_parkir', 'map_width')) {
                $table->unsignedInteger('map_width')->default(1000)->after('map_image');
            }
            if (!Schema::hasColumn('tb_area_parkir', 'map_height')) {
                $table->unsignedInteger('map_height')->default(800)->after('map_width');
            }
            if (!Schema::hasColumn('tb_area_parkir', 'is_default_map')) {
                $table->boolean('is_default_map')->default(false)->after('map_height');
            }
        });

        // 2. Pindahkan data dari tb_parking_maps ke tb_area_parkir (hanya jika tb_parking_maps masih ada)
        if (Schema::hasTable('tb_parking_maps')) {
            $maps = DB::table('tb_parking_maps')->get();
            foreach ($maps as $map) {
                DB::table('tb_area_parkir')
                    ->where('id_area', $map->area_parkir_id)
                    ->update([
                        'map_code' => $map->code,
                        'map_image' => $map->image_path,
                        'map_width' => $map->width,
                        'map_height' => $map->height,
                        'is_default_map' => $map->is_default,
                    ]);
            }

            // 3. Update foreign keys di tabel relasi (slots & cameras)
            // Tambahkan kolom area_parkir_id ke tb_parking_map_cameras
            Schema::table('tb_parking_map_cameras', function (Blueprint $table) {
                if (!Schema::hasColumn('tb_parking_map_cameras', 'area_parkir_id')) {
                    $table->unsignedBigInteger('area_parkir_id')->nullable()->after('parking_map_id');
                    $table->foreign('area_parkir_id')->references('id_area')->on('tb_area_parkir')->cascadeOnDelete();
                }
            });

            // Isi data area_parkir_id berdasarkan parking_map_id
            foreach ($maps as $map) {
                if (Schema::hasColumn('tb_parking_map_cameras', 'parking_map_id')) {
                    DB::table('tb_parking_map_cameras')
                        ->where('parking_map_id', $map->id)
                        ->update(['area_parkir_id' => $map->area_parkir_id]);
                }
                
                if (Schema::hasColumn('tb_parking_map_slots', 'parking_map_id')) {
                    DB::table('tb_parking_map_slots')
                        ->where('parking_map_id', $map->id)
                        ->update(['area_parkir_id' => $map->area_parkir_id]);
                }
            }

            // 4. Hapus kolom parking_map_id dan hapus tabel tb_parking_maps
            Schema::table('tb_parking_map_slots', function (Blueprint $table) {
                if (Schema::hasColumn('tb_parking_map_slots', 'parking_map_id')) {
                    $table->dropForeign(['parking_map_id']);
                    $table->dropColumn('parking_map_id');
                }
                
                // Change it from nullOnDelete to cascadeOnDelete safely
                try {
                    $table->dropForeign(['area_parkir_id']);
                } catch (\Exception $e) {}
                
                $table->unsignedBigInteger('area_parkir_id')->nullable(false)->change();
                $table->foreign('area_parkir_id')->references('id_area')->on('tb_area_parkir')->cascadeOnDelete();
            });

            Schema::table('tb_parking_map_cameras', function (Blueprint $table) {
                if (Schema::hasColumn('tb_parking_map_cameras', 'parking_map_id')) {
                    $table->dropForeign(['parking_map_id']);
                    $table->dropColumn('parking_map_id');
                }
                if (Schema::hasColumn('tb_parking_map_cameras', 'area_parkir_id')) {
                    $table->unsignedBigInteger('area_parkir_id')->nullable(false)->change();
                }
            });

            Schema::dropIfExists('tb_parking_maps');
        }
    }

    public function down(): void
    {
        // Reversal logic is complex and usually not needed in this specific task context,
        // but for safety, we should at least drop the added columns.
        Schema::table('tb_area_parkir', function (Blueprint $table) {
            $table->dropColumn(['map_code', 'map_image', 'map_width', 'map_height', 'is_default_map']);
        });
    }
};
