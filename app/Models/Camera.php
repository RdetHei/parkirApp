<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    public const TIPE_SCANNER = 'scanner';
    public const TIPE_VIEWER = 'viewer';

    protected $table = 'tb_kamera';

    protected $fillable = [
        'nama',
        'url',
        'tipe',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public static function tipeOptions(): array
    {
        return [
            self::TIPE_SCANNER => 'Kamera Scanner (scan plat di Catat Masuk)',
            self::TIPE_VIEWER => 'Viewer (tampil di map interaktif)',
        ];
    }

    public function isScanner(): bool
    {
        return $this->tipe === self::TIPE_SCANNER;
    }

    public function isViewer(): bool
    {
        return $this->tipe === self::TIPE_VIEWER;
    }

    public static function getDefaultOrFirst()
    {
        $default = static::where('is_default', true)->first();
        return $default ?? static::orderBy('id')->first();
    }

    public function scopeScanner($query)
    {
        return $query->where('tipe', self::TIPE_SCANNER);
    }

    public function scopeViewer($query)
    {
        return $query->where('tipe', self::TIPE_VIEWER);
    }
}
