<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingMapSlot extends Model
{
    protected $table = 'tb_parking_map_slots';

    protected $fillable = [
        'parking_map_id',
        'code',
        'x',
        'y',
        'width',
        'height',
        'area_parkir_id',
        'camera_id',
        'notes',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function parkingMap()
    {
        return $this->belongsTo(ParkingMap::class, 'parking_map_id', 'id');
    }

    public function areaParkir()
    {
        return $this->belongsTo(AreaParkir::class, 'area_parkir_id', 'id_area');
    }

    public function camera()
    {
        return $this->belongsTo(Camera::class, 'camera_id', 'id');
    }
}
