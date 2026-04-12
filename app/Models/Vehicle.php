<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = ['user_id', 'plate_number', 'vehicle_type'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rfidTag()
    {
        return $this->hasOne(RfidTag::class);
    }

    public function parkingLogs()
    {
        return $this->hasMany(ParkingLog::class);
    }
}
