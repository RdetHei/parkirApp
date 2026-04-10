<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaParkir extends Model
{
    protected $table = 'tb_area_parkir';
    protected $primaryKey = 'id_area';
    protected $fillable = [
        'nama_area',
        'map_prefix',
        'daerah',
        'kapasitas',
        'terisi',
        'map_code',
        'map_image',
        'map_width',
        'map_height',
        'is_default_map',
    ];

    protected $casts = [
        'is_default_map' => 'boolean',
    ];

    protected $appends = [
        'map_image_url',
    ];

    public function getRouteKeyName()
    {
        return 'id_area';
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'id_area', 'id_area');
    }

    public function slots()
    {
        return $this->hasMany(ParkingMapSlot::class, 'area_parkir_id', 'id_area');
    }

    public function mapCameras()
    {
        return $this->hasMany(ParkingMapCamera::class, 'area_parkir_id', 'id_area');
    }

    public static function getDefaultMap(): ?self
    {
        return static::where('is_default_map', true)->first() ?? static::first();
    }

    public function getMapImageUrlAttribute(): ?string
    {
        $value = $this->map_image;
        if (!$value) {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        return asset('storage/' . ltrim($value, '/'));
    }

    /**
     * Mencari satu slot pertama yang tersedia di area tersebut.
     * Kriterianya: slot tidak memiliki transaksi aktif (status 'masuk')
     * dan tidak sedang dalam reservasi aktif.
     */
    public function findNextAvailableSlot(): ?ParkingMapSlot
    {
        return $this->slots()
            ->whereDoesntHave('transaksis', function ($query) {
                $query->where('status', 'masuk');
            })
            ->whereDoesntHave('reservations', function ($query) {
                $query->where('expires_at', '>', now());
            })
            ->orderBy('code', 'asc')
            ->first();
    }
}
