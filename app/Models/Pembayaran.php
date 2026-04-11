<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembayaran extends Model
{
    use SoftDeletes;

    protected $table = 'tb_pembayaran';
    protected $primaryKey = 'id_pembayaran';
    protected $fillable = [
        'id_parkir',
        'order_id',
        'transaction_id',
        'payment_type',
        'nominal',
        'metode',
        'status',
        'keterangan',
        'id_user',
        'id_kas_shift',
        'cash_received',
        'cash_change',
        'waktu_pembayaran',
    ];

    protected $casts = [
        'waktu_pembayaran' => 'datetime',
        'nominal' => 'decimal:2',
        'cash_received' => 'decimal:2',
        'cash_change' => 'decimal:2',
    ];

    // Relationships
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'id_parkir', 'id_parkir');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeBerhasil($query)
    {
        return $query->where('status', 'berhasil');
    }

    public function scopeMidtrans($query)
    {
        return $query->where('metode', 'midtrans');
    }

    public function scopeCash($query)
    {
        return $query->where('metode', 'cash');
    }
}
