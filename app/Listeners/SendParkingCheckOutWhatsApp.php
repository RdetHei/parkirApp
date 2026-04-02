<?php

namespace App\Listeners;

use App\Events\ParkingCheckedOut;
use App\Services\WhatsAppGateway;

class SendParkingCheckOutWhatsApp
{
    public function __construct(protected WhatsAppGateway $whatsapp) {}

    public function handle(ParkingCheckedOut $event): void
    {
        $transaksi = $event->transaksi->loadMissing(['kendaraan', 'user', 'tarif']);
        $user = $transaksi->notifyTargetUser();
        if (! $user) {
            return;
        }

        $plat = $transaksi->kendaraan?->plat_nomor ?? '-';
        $keluar = $transaksi->waktu_keluar?->timezone(config('app.timezone'))?->format('d/m/Y H:i') ?? '-';
        $durasi = $transaksi->durasi_jam ?? '-';
        $biaya = isset($transaksi->biaya_total)
            ? 'Rp '.number_format((float) $transaksi->biaya_total, 0, ',', '.')
            : '-';

        $msg = "*NESTON — Check-out berhasil*\n\n"
            ."Nama: {$user->name}\n"
            ."Plat: {$plat}\n"
            ."Waktu keluar: {$keluar}\n"
            ."Durasi: {$durasi} jam\n"
            ."Total biaya: {$biaya}";

        $this->whatsapp->sendToUser($user, $msg);
    }
}
