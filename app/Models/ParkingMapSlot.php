<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingMapSlot extends Model
{
    protected $table = 'tb_parking_map_slots';

    protected $fillable = [
        'area_parkir_id',
        'code',
        'x',
        'y',
        'width',
        'height',
        'camera_id',
        'notes',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function areaParkir()
    {
        return $this->belongsTo(AreaParkir::class, 'area_parkir_id', 'id_area');
    }

    public function camera()
    {
        return $this->belongsTo(Camera::class, 'camera_id', 'id');
    }

    /**
     * Slot yang terikat ke area.
     */
    public function scopeForArea($query, AreaParkir $area)
    {
        return $query->where('area_parkir_id', $area->id_area);
    }

    /** id_area efektif untuk filter UI. */
    public function effectiveAreaId(): ?int
    {
        return $this->area_parkir_id ? (int) $this->area_parkir_id : null;
    }
}
