<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaParkir extends Model
{
    protected $table = 'tb_area_parkir';
    protected $primaryKey = 'id_area';
    protected $fillable = [
        'nama_area',
        'kapasitas',
        'terisi',
        'map_code',
        'map_image',
        'map_width',
        'map_height',
        'is_default_map',
    ];

    protected $casts = [
        'is_default_map' => 'boolean',
    ];

    public function getRouteKeyName()
    {
        return 'id_area';
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_area', 'id_area');
    }

    public function slots()
    {
        return $this->hasMany(ParkingMapSlot::class, 'area_parkir_id', 'id_area');
    }

    public function mapCameras()
    {
        return $this->hasMany(ParkingMapCamera::class, 'area_parkir_id', 'id_area');
    }

    public static function getDefaultMap(): ?self
    {
        return static::where('is_default_map', true)->first() ?? static::first();
    }
}
