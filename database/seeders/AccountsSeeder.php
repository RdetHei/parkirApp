<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\AreaParkir;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Akun bawaan untuk pengembangan / demo.
 * Petugas: id_area null — area diaktifkan dengan Kode Peta setelah login.
 */
class AccountsSeeder extends Seeder
{
    public function run(): void
    {
        if (! AreaParkir::query()->exists()) {
            $this->command->error('Belum ada area parkir. Jalankan: php artisan db:seed --class=AreaParkirSeeder');

            return;
        }

        User::updateOrCreate(
            ['email' => 'owner@gmail.com'],
            [
                'name' => 'Owner',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'balance' => 0,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'balance' => 0,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'petugas@gmail.com'],
            [
                'name' => 'Petugas',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'balance' => 0,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
                'role' => 'user',
                'balance' => 100000,
                'email_verified_at' => now(),
            ],
        );

        $this->command->info('Data seeder berhasil ditambahkan:');
        $this->command->info('- Owner: owner@gmail.com | password');
        $this->command->info('- Admin: admin@gmail.com | password');
        $this->command->info('- Petugas: petugas@gmail.com | password');
        $this->command->info('- User: user@gmail.com | password (saldo: 100rb)');
    }
}
