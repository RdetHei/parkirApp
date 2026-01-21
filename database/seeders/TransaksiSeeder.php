<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaksi;
use App\Models\Kendaraan;
use App\Models\Tarif;
use App\Models\AreaParkir;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $kendaraan1 = Kendaraan::where('plat_nomor','B1234CD')->first();
        $kendaraan2 = Kendaraan::where('plat_nomor','D4321XY')->first();
        $kendaraan3 = Kendaraan::where('plat_nomor','F5555ZZ')->first();

        $tarifMobil = Tarif::where('jenis_kendaraan','mobil')->first();
        $tarifMotor = Tarif::where('jenis_kendaraan','motor')->first();

        $area1 = AreaParkir::first();

        // Active (masuk) transactions
        Transaksi::create([
            'id_kendaraan' => $kendaraan1->id_kendaraan,
            'waktu_masuk' => Carbon::now()->subHours(2),
            'id_tarif' => $tarifMobil->id_tarif,
            'status' => 'masuk',
            'id_user' => 1,
            'id_area' => $area1->id_area,
            'created_at' => Carbon::now()->subHours(2),
            'updated_at' => Carbon::now()->subHours(2),
        ]);

        // Exited transaction
        $tx = Transaksi::create([
            'id_kendaraan' => $kendaraan2->id_kendaraan,
            'waktu_masuk' => Carbon::now()->subHours(5),
            'waktu_keluar' => Carbon::now()->subHours(1),
            'id_tarif' => $tarifMotor->id_tarif,
            'durasi_jam' => 4,
            'biaya_total' => $tarifMotor->tarif_perjam * 4,
            'status' => 'keluar',
            'status_pembayaran' => 'belum_bayar',
            'id_user' => 1,
            'id_area' => $area1->id_area,
            'created_at' => Carbon::now()->subHours(5),
            'updated_at' => Carbon::now()->subHours(1),
        ]);

        // Another exited and paid transaction
        $tx2 = Transaksi::create([
            'id_kendaraan' => $kendaraan3->id_kendaraan,
            'waktu_masuk' => Carbon::now()->subHours(10),
            'waktu_keluar' => Carbon::now()->subHours(6),
            'id_tarif' => $tarifMotor->id_tarif,
            'durasi_jam' => 4,
            'biaya_total' => $tarifMotor->tarif_perjam * 4,
            'status' => 'keluar',
            'status_pembayaran' => 'sudah_bayar',
            'id_user' => 1,
            'id_area' => $area1->id_area,
            'created_at' => Carbon::now()->subHours(10),
            'updated_at' => Carbon::now()->subHours(6),
        ]);

        // decrement/increment area terisi values are handled by observer in app, but ensure area terisi is realistic
        AreaParkir::where('id_area',$area1->id_area)->increment('terisi', 1);
    }
}
