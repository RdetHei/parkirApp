<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AccountsSeeder::class,
            AreaParkirTableSeeder::class,
            TarifTableSeeder::class,
            KendaraanTableSeeder::class,
            // KameraTableSeeder::class, // Dikosongkan sesuai instruksi user
            ParkingMapTableSeeder::class,
            ParkingSlotTableSeeder::class,
            TransaksiTableSeeder::class,
            PembayaranTableSeeder::class,
            LogAktivitasTableSeeder::class,
            SaldoHistoryTableSeeder::class,
        ]);
    }
}
