<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RfidTransaction extends Model
{
    protected $table = 'tb_rfid_transactions';

    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'created_at',
    ];

    // Spec: only created_at (no updated_at)
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

