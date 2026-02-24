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
    ];

    public function getRouteKeyName()
    {
        return 'id_area';
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_area', 'id_area');
    }

    /** Satu area punya satu layout peta (1:1). Dibuat otomatis saat area dibuat. */
    public function parkingMap()
    {
        return $this->hasOne(ParkingMap::class, 'area_parkir_id', 'id_area');
    }
}
