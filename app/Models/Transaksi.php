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
        'bookmarked_at',
        'catatan',
        'id_user',
        'id_area',
        'parking_map_slot_id',
        'status_pembayaran',
        'id_pembayaran',
        'midtrans_order_id',
        'diskon',
    ];

    protected $casts = [
        'waktu_masuk' => 'datetime',
        'waktu_keluar' => 'datetime',
        'bookmarked_at' => 'datetime',
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

    public function parkingMapSlot()
    {
        return $this->belongsTo(ParkingMapSlot::class, 'parking_map_slot_id', 'id');
    }

    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class, 'id_pembayaran', 'id_pembayaran');
    }

    /**
     * User yang menerima notifikasi parkir: pemilik kendaraan jika ada, jika tidak fallback ke id_user transaksi.
     */
    public function notifyTargetUser(): ?User
    {
        $this->loadMissing(['kendaraan.user', 'user']);

        if ($this->kendaraan?->id_user) {
            return $this->kendaraan->user;
        }

        return $this->user;
    }

    // Accessors & Mutators
    public function getDurasiJamAttribute()
    {
        // Untuk transaksi yang sudah keluar, prioritaskan nilai yang tersimpan di database (jika ada dan positif)
        if ($this->status === 'keluar' && !empty($this->attributes['durasi_jam']) && $this->attributes['durasi_jam'] > 0) {
            return (int) $this->attributes['durasi_jam'];
        }

        // Untuk transaksi aktif atau jika nilai di database tidak valid/negatif, hitung ulang secara real-time
        if ($this->waktu_masuk) {
            $masuk = \Illuminate\Support\Carbon::parse($this->waktu_masuk);
            $keluar = $this->waktu_keluar ? \Illuminate\Support\Carbon::parse($this->waktu_keluar) : \Illuminate\Support\Carbon::now();

            // Pastikan timezone sinkron sebelum kalkulasi
            $masuk->setTimezone(config('app.timezone'));
            $keluar->setTimezone(config('app.timezone'));

            // Gunakan absolute true untuk mencegah nilai negatif
            $durasi_menit = $keluar->diffInMinutes($masuk, true);

            // Minimal 1 jam untuk durasi parkir
            return (int) max(1, ceil($durasi_menit / 60));
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
            $subtotal = $durasi * $tarif_perjam;

            // Diskon 10% jika user memiliki rfid_uid (kartu member)
            if ($this->id_user) {
                $user = $this->user ?: User::find($this->id_user);
                if ($user && !empty($user->rfid_uid)) {
                    $subtotal = $subtotal * 0.9;
                }
            }

            return $subtotal;
        }
        return 0;
    }
}
