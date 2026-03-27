<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NfcTransaction extends Model
{
    protected $table = 'tb_nfc_transactions';

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'created_at',
    ];

    // Migration sesuai spec: hanya created_at (tanpa updated_at).
    public $timestamps = false;

    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

