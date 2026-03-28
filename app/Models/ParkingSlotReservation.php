<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingSlotReservation extends Model
{
    protected $table = 'tb_parking_slot_reservations';

    protected $fillable = [
        'parking_map_slot_id',
        'id_area',
        'id_user',
        'id_kendaraan',
        'id_tarif',
        'reserved_at',
        'expires_at',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function slot()
    {
        return $this->belongsTo(ParkingMapSlot::class, 'parking_map_slot_id', 'id');
    }

    public function area()
    {
        return $this->belongsTo(AreaParkir::class, 'id_area', 'id_area');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'id_tarif', 'id_tarif');
    }

    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }
}

