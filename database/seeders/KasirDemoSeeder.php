<?php

namespace Database\Seeders;

use App\Models\AreaParkir;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun petugas untuk uji loket/tunai (bukan role terpisah).
 * Jalankan: php artisan db:seed --class=KasirDemoSeeder
 */
class KasirDemoSeeder extends Seeder
{
    public function run(): void
    {
        $area = AreaParkir::query()->orderBy('id_area')->first();
        if (! $area) {
            $this->command->error('Belum ada area parkir. Jalankan: php artisan db:seed --class=AreaParkirSeeder');

            return;
        }

        User::updateOrCreate(
            ['email' => 'petugas.loket@neston.local'],
            [
                'name' => 'Petugas Loket (Demo)',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'id_area' => $area->id_area,
                'balance' => 0,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Petugas loket demo — email: petugas.loket@neston.local | password: password | area: '.$area->nama_area);
    }
}
