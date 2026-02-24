<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingMapCamera extends Model
{
    protected $table = 'tb_parking_map_cameras';

    protected $fillable = [
        'parking_map_id',
        'camera_id',
        'x',
        'y',
    ];

    public function parkingMap()
    {
        return $this->belongsTo(ParkingMap::class, 'parking_map_id', 'id');
    }

    public function camera()
    {
        return $this->belongsTo(Camera::class, 'camera_id', 'id');
    }
}
