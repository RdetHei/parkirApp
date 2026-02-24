<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingMap extends Model
{
    protected $table = 'tb_parking_maps';

    protected $fillable = [
        'area_parkir_id',
        'name',
        'code',
        'image_path',
        'width',
        'height',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function slots()
    {
        return $this->hasMany(ParkingMapSlot::class, 'parking_map_id', 'id');
    }

    public function mapCameras()
    {
        return $this->hasMany(ParkingMapCamera::class, 'parking_map_id', 'id');
    }

    public function areaParkir()
    {
        return $this->belongsTo(AreaParkir::class, 'area_parkir_id', 'id_area');
    }

    public static function getDefaultOrFirst(): ?self
    {
        $default = static::where('is_default', true)->first();
        return $default ?? static::orderBy('id')->first();
    }
}

