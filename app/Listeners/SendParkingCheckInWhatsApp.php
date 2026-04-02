<?php

namespace App\Listeners;

use App\Events\ParkingCheckedIn;
use App\Services\WhatsAppGateway;

class SendParkingCheckInWhatsApp
{
    public function __construct(protected WhatsAppGateway $whatsapp) {}

    public function handle(ParkingCheckedIn $event): void
    {
        $transaksi = $event->transaksi->loadMissing(['kendaraan', 'area', 'user']);
        $user = $transaksi->notifyTargetUser();
        if (! $user) {
            return;
        }

        $plat = $transaksi->kendaraan?->plat_nomor ?? '-';
        $masuk = $transaksi->waktu_masuk?->timezone(config('app.timezone'))?->format('d/m/Y H:i') ?? '-';
        $lokasi = $transaksi->area?->nama_area ?? '-';

        $msg = "*NESTON — Check-in berhasil*\n\n"
            ."Nama: {$user->name}\n"
            ."Plat: {$plat}\n"
            ."Waktu masuk: {$masuk}\n"
            ."Lokasi: {$lokasi}";

        $this->whatsapp->sendToUser($user, $msg);
    }
}
