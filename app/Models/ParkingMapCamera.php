<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingMapCamera extends Model
{
    protected $table = 'tb_parking_map_cameras';

    protected $fillable = [
        'area_parkir_id',
        'camera_id',
        'x',
        'y',
    ];

    public function areaParkir()
    {
        return $this->belongsTo(AreaParkir::class, 'area_parkir_id', 'id_area');
    }

    public function camera()
    {
        return $this->belongsTo(Camera::class, 'camera_id', 'id');
    }
}
