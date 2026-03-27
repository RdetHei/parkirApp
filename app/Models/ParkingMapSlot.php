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

    /**
     * Slot yang terikat ke area: lewat area_parkir_id di slot, atau lewat peta parkir area tersebut.
     */
    public function scopeForArea($query, AreaParkir $area)
    {
        $mapIds = ParkingMap::where('area_parkir_id', $area->id_area)->pluck('id');

        return $query->where(function ($q) use ($area, $mapIds) {
            $q->where('area_parkir_id', $area->id_area);
            if ($mapIds->isNotEmpty()) {
                $q->orWhereIn('parking_map_id', $mapIds);
            }
        });
    }

    /** id_area efektif untuk filter UI (slot bisa hanya punya parking_map_id). */
    public function effectiveAreaId(): ?int
    {
        if ($this->area_parkir_id) {
            return (int) $this->area_parkir_id;
        }

        return $this->parkingMap?->area_parkir_id ? (int) $this->parkingMap->area_parkir_id : null;
    }
}
