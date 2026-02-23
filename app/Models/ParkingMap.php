<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkingMap extends Model
{
    protected $table = 'tb_parking_maps';

    protected $fillable = [
        'name',
        'code',
        'image_path',
        'width',
        'height',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public static function getDefaultOrFirst(): ?self
    {
        $default = static::where('is_default', true)->first();
        return $default ?? static::orderBy('id')->first();
    }
}

