<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfidTag extends Model
{
    protected $fillable = ['uid', 'vehicle_id', 'status'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
