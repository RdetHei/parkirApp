<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use SoftDeletes;

    protected $table = 'tb_transaksi';
    protected $primaryKey = 'id_parkir';
    protected $fillable = [
        'id_kendaraan',
        'waktu_masuk',
        'waktu_keluar',
        'id_tarif',
        'durasi_jam',
        'biaya_total',
        'status',
        'catatan',
        'id_user',
        'id_area',
        'status_pembayaran',
        'id_pembayaran',
        'midtrans_order_id',
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
        'biaya_total' => 'decimal:2',
        'durasi_jam' => 'integer',
    ];

    // Relationships
    public function kendaraan()
    {
        return $this->belongsTo(Kendaraan::class, 'id_kendaraan', 'id_kendaraan');
    }

    public function tarif()
    {
        return $this->belongsTo(Tarif::class, 'id_tarif', 'id_tarif');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function area()
    {
        return $this->belongsTo(AreaParkir::class, 'id_area', 'id_area');
    }

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'id_pembayaran', 'id_pembayaran');
    }

    // Accessors & Mutators
    public function getDurasiJamAttribute()
    {
        // Jika sudah ada value di database, gunakan itu
        if ($this->attributes['durasi_jam'] ?? null) {
            return $this->attributes['durasi_jam'];
        }

        // Jika belum ada, hitung dari waktu masuk dan keluar
        if ($this->waktu_masuk && $this->waktu_keluar) {
            $masuk = \Carbon\Carbon::parse($this->waktu_masuk);
            $keluar = \Carbon\Carbon::parse($this->waktu_keluar);
            return ceil($keluar->diffInMinutes($masuk) / 60);
        }
        return null;
    }

    public function getBiayaTotalAttribute()
    {
        // Jika sudah ada value di database, gunakan itu
        if ($this->attributes['biaya_total'] ?? null) {
            return $this->attributes['biaya_total'];
        }

        // Jika belum ada, hitung dari durasi dan tarif
        if ($this->waktu_masuk && $this->waktu_keluar && $this->tarif) {
            $durasi = $this->getDurasiJamAttribute();
            $tarif_perjam = $this->tarif->tarif_perjam;
            return $durasi * $tarif_perjam;
        }
        return 0;
    }
}
