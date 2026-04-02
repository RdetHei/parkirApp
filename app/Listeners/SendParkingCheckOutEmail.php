<?php

namespace App\Listeners;

use App\Events\ParkingCheckedOut;
use App\Mail\ParkingCheckOutMail;
use App\Models\NotificationLog;
use Illuminate\Support\Facades\Mail;

class SendParkingCheckOutEmail
{
    public function handle(ParkingCheckedOut $event): void
    {
        $transaksi = $event->transaksi->loadMissing(['kendaraan', 'area', 'user', 'tarif']);
        $user = $transaksi->notifyTargetUser();
        if (! $user || blank($user->email)) {
            return;
        }

        try {
            Mail::to($user->email)->send(new ParkingCheckOutMail($transaksi));
            NotificationLog::create([
                'user_id' => $user->id,
                'type' => 'email',
                'status' => 'success',
                'message' => 'ParkingCheckOutMail terkirim (check-out #'.$transaksi->id_parkir.')',
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
