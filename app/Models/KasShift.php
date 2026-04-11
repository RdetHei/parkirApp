<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KasShift extends Model
{
    protected $table = 'tb_kas_shift';
    protected $primaryKey = 'id_kas_shift';

    protected $fillable = [
        'opened_by',
        'closed_by',
        'id_area',
        'opened_at',
        'closed_at',
        'opening_cash_amount',
        'closing_note',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_cash_amount' => 'decimal:2',
    ];

    public function openedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by', 'id');
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by', 'id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(AreaParkir::class, 'id_area', 'id_area');
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'id_kas_shift', 'id_kas_shift');
    }

    public function isClosed(): bool
    {
        return $this->closed_at !== null;
    }

    /**
     * Shift kasir yang masih terbuka untuk user + area (satu shift aktif per kombinasi).
     */
    public static function openShiftFor(int $userId, ?int $areaId): ?self
    {
        return static::query()
            ->where('opened_by', $userId)
            ->whereNull('closed_at')
            ->when($areaId !== null, fn ($q) => $q->where('id_area', $areaId))
            ->when($areaId === null, fn ($q) => $q->whereNull('id_area'))
            ->orderByDesc('opened_at')
            ->first();
    }
}
