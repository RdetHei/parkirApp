<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountsSeeder extends Seeder
{
    public function run(): void
    {
        User::withTrashed()->forceDelete();

        $defaultPassword = Hash::make('12345678');

        User::insert([
            [
                'id' => 1,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'email_verified_at' => '2026-02-20 04:12:55',
                'password' => $defaultPassword,
                'role' => 'user',
                'saldo' => 500000,
                'remember_token' => 'L7anO6aS98',
                'created_at' => '2026-02-20 04:12:55',
                'updated_at' => '2026-02-20 04:12:55',
                'deleted_at' => null,
            ],
            [
                'id' => 2,
                'name' => 'Rudi',
                'email' => 'admin@gmail.com',
                'email_verified_at' => '2026-02-20 04:12:56',
                'password' => $defaultPassword,
                'role' => 'admin',
                'saldo' => 1000000,
                'remember_token' => 'AEpwjkrVhv4K4s9sLBgQP6qQBC55RDyW0vU3z0ge3udlQeKl9o2Jc7NGUAau',
                'created_at' => '2026-02-20 04:12:56',
                'updated_at' => '2026-02-20 04:12:56',
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'name' => 'Petugas',
                'email' => 'petugas@gmail.com',
                'email_verified_at' => '2026-02-20 04:12:57',
                'password' => $defaultPassword,
                'role' => 'petugas',
                'saldo' => 0,
                'remember_token' => 'L6wQ5bhXkg',
                'created_at' => '2026-02-20 04:12:57',
                'updated_at' => '2026-02-20 04:12:57',
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'name' => 'Owner',
                'email' => 'owner@gmail.com',
                'email_verified_at' => '2026-02-20 04:12:58',
                'password' => $defaultPassword,
                'role' => 'owner',
                'saldo' => 2000000,
                'remember_token' => 'udhXpAjD29',
                'created_at' => '2026-02-20 04:12:58',
                'updated_at' => '2026-02-20 04:12:58',
                'deleted_at' => null,
            ],
        ]);
    }
}