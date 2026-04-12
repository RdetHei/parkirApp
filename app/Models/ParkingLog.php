<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingLog extends Model
{
    protected $fillable = [
        'id_kendaraan',
        'checkin_time',
        'checkout_time',
        'gate_type',
        'tariff_amount'
    ];

    protected $casts = [
        'checkin_time' => 'datetime',
        'checkout_time' => 'datetime'
    ];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }
}
