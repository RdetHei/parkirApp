<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::updateOrCreate(
            ['email' => 'admin@neston.local'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'id_area' => null,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'petugas@neston.local'],
            [
                'name' => 'Petugas Lapangan',
                'password' => Hash::make('password'),
                'role' => 'petugas',
                'id_area' => null,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'owner@neston.local'],
            [
                'name' => 'Owner',
                'password' => Hash::make('password'),
                'role' => 'owner',
                'id_area' => null,
                'email_verified_at' => now(),
            ],
        );

        $this->command?->info('Akun demo: admin@neston.local | petugas@neston.local | owner@neston.local — password: password');
    }
}
