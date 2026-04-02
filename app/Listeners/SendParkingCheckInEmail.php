<?php

namespace App\Listeners;

use App\Events\ParkingCheckedIn;
use App\Mail\ParkingCheckInMail;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;

class SendParkingCheckInEmail
{
    public function handle(ParkingCheckedIn $event): void
    {
        $transaksi = $event->transaksi->loadMissing(['kendaraan', 'area', 'user']);
        $user = $transaksi->notifyTargetUser();
        if (! $user || blank($user->email)) {
            return;
        }

        try {
            Mail::to($user->email)->send(new ParkingCheckInMail($transaksi));
            NotificationLog::create([
                'user_id' => $user->id,
                'type' => 'email',
                'status' => 'success',
                'message' => 'ParkingCheckInMail terkirim (check-in #'.$transaksi->id_parkir.')',
            ]);
        } catch (\Throwable $e) {
            NotificationLog::create([
                'user_id' => $user->id,
                'type' => 'email',
                'status' => 'failed',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
