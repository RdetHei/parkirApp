<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AreaParkir;
use App\Models\Kendaraan;
use App\Models\Tarif;
use App\Models\Transaksi;
use App\Models\LogAktifitas;
use App\Models\Pembayaran;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $areas = AreaParkir::all();
        $tarifs = Tarif::all();

        if ($areas->isEmpty() || $tarifs->isEmpty()) {
            $this->command->error('Pastikan AreaParkir dan Tarif sudah memiliki data dasar!');
            return;
        }

        // 1. Tambah 100 User Reguler
        $this->command->info('Membuat 100 User...');
        $users = [];
        for ($i = 0; $i < 100; $i++) {
            $users[] = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'phone' => $faker->phoneNumber,
                'password' => Hash::make('password'),
                'role' => 'user',
                'balance' => $faker->numberBetween(5000, 200000),
                'rfid_uid' => $faker->unique()->hexColor, // UID Dummy
                'email_verified_at' => now(),
            ]);
        }

        // 2. Tambah 150 Kendaraan (acak untuk 100 user tadi)
        $this->command->info('Membuat 150 Kendaraan...');
        $vehicles = [];
        $jenis = ['motor', 'mobil'];
        foreach ($users as $user) {
            $count = $faker->numberBetween(1, 2);
            for ($j = 0; $j < $count; $j++) {
                $vehicles[] = Kendaraan::create([
                    'id_user' => $user->id,
                    'plat_nomor' => strtoupper($faker->bothify('? #### ??')),
                    'jenis_kendaraan' => $faker->randomElement($jenis),
                    'warna' => $faker->safeColorName,
                    'pemilik' => $user->name,
                ]);
            }
        }

        // 3. Tambah 1000 Transaksi (Riwayat 30 hari terakhir)
        $this->command->info('Membuat 200 Transaksi (ini butuh waktu)...');
        $statuses = ['keluar', 'masuk'];
        for ($k = 0; $k < 200; $k++) {
            $vehicle = $faker->randomElement($vehicles);
            $area = $faker->randomElement($areas);
            $tarif = $tarifs->where('jenis_kendaraan', $vehicle->jenis_kendaraan)->first() ?? $tarifs->first();

            $waktuMasuk = Carbon::now()->subDays($faker->numberBetween(1, 30))->subHours($faker->numberBetween(1, 24));
            $status = $faker->randomElement(['keluar', 'keluar', 'keluar', 'masuk']); // Lebih banyak yang sudah keluar

            $transaksi = Transaksi::create([
                'id_user' => $vehicle->id_user,
                'id_kendaraan' => $vehicle->id_kendaraan,
                'id_area' => $area->id_area,
                'id_tarif' => $tarif->id_tarif,
                'waktu_masuk' => $waktuMasuk,
                'status' => $status,
                'status_pembayaran' => $status === 'keluar' ? 'berhasil' : 'pending',
            ]);

            if ($status === 'keluar') {
                $durasi = $faker->numberBetween(1, 10);
                $biaya = $durasi * $tarif->tarif_perjam;
                $waktuKeluar = (clone $waktuMasuk)->addHours($durasi);

                $transaksi->update([
                    'waktu_keluar' => $waktuKeluar,
                    'durasi_jam' => $durasi,
                    'biaya_total' => $biaya,
                ]);

                // Buat data pembayaran
                Pembayaran::create([
                    'id_parkir' => $transaksi->id_parkir,
                    'nominal' => $biaya,
                    'metode' => $faker->randomElement(['nestonpay', 'cash']),
                    'status' => 'berhasil',
                    'id_user' => 1, // Admin default
                    'waktu_pembayaran' => $waktuKeluar,
                ]);
            }
        }

        // 4. Tambah 500 Log Aktivitas
        $this->command->info('Membuat 250 Log Aktivitas...');
        $types = ['login', 'transaction', 'config', 'profile'];
        for ($l = 0; $l < 250; $l++) {
            LogAktifitas::create([
                'id_user' => $faker->randomElement($users)->id,
                'aktivitas' => $faker->sentence(6),
                'tipe_aktivitas' => $faker->randomElement($types),
                'waktu_aktivitas' => Carbon::now()->subDays($faker->numberBetween(0, 15)),
            ]);
        }

        $this->command->info('Selesai! Data dummy berhasil dibuat.');
    }
}
