<?php

namespace App\Observers;

use App\Events\ParkingCheckedIn;
use App\Events\ParkingCheckedOut;
use App\Models\Transaksi;
use App\Models\LogAktifitas;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransaksiObserver
{
    /**
     * Handle the Transaksi "created" event.
     */
    public function created(Transaksi $transaksi): void
    {
        if (Auth::check()) {
            LogAktifitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Membuat transaksi parkir #' . str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT),
                'waktu_aktivitas' => Carbon::now(),
            ]);
        }

        if (
            $transaksi->status === 'masuk'
            && $transaksi->waktu_masuk
            && $transaksi->id_user
        ) {
            $transaksi->loadMissing(['kendaraan', 'area', 'user']);
            event(new ParkingCheckedIn($transaksi));
        }
    }

    /**
     * Handle the Transaksi "updated" event.
     */
    public function updated(Transaksi $transaksi): void
    {
        if (Auth::check()) {
            $activity = 'Mengupdate transaksi parkir #' . str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT);

            if ($transaksi->isDirty('status') && $transaksi->status === 'keluar') {
                $activity = 'Mencatat kendaraan keluar parkir #' . str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT);
            }

            LogAktifitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => $activity,
                'waktu_aktivitas' => Carbon::now(),
            ]);
        }

        $checkoutStatus = in_array($transaksi->status, ['keluar', 'selesai'], true);
        if (
            $transaksi->wasChanged('waktu_keluar')
            && $transaksi->getOriginal('waktu_keluar') === null
            && $transaksi->waktu_keluar
            && $checkoutStatus
        ) {
            $transaksi->loadMissing(['kendaraan', 'area', 'user', 'tarif']);
            if ($transaksi->notifyTargetUser()) {
                event(new ParkingCheckedOut($transaksi));
            }
        }
    }

    /**
     * Handle the Transaksi "deleted" event.
     */
    public function deleted(Transaksi $transaksi): void
    {
        if (Auth::check()) {
            LogAktifitas::create([
                'id_user' => Auth::id(),
                'aktivitas' => 'Menghapus transaksi parkir #' . str_pad($transaksi->id_parkir, 8, '0', STR_PAD_LEFT),
                'waktu_aktivitas' => Carbon::now(),
            ]);
        }
    }
}
