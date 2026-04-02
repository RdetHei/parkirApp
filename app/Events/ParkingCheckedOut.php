<?php

namespace App\Events;

use App\Models\Transaksi;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParkingCheckedOut
{
    use Dispatchable, SerializesModels;

    public function __construct(public Transaksi $transaksi) {}
}
