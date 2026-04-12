<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfidTag extends Model
{
    protected $fillable = ['uid', 'id_kendaraan', 'status'];

    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }
}
