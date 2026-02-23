<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    public function run(): void
    {
        // Bersihkan data lama
        User::query()->delete();

        // Data sesuai dump parkir.sql
        User::insert([
            [
                'id' => 1,
                'name' => 'Test User',
                'email' => 'test@example.com',
                'email_verified_at' => '2026-02-20 04:12:55',
                'password' => '$2y$12$/0ab1dF7IT27SE6j6.sUEurzN9EnGYIECD9oVE0yrABCxzCotHl4.',
                'role' => 'user',
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
                'password' => '$2y$12$8sZS/viHyixCfahKdQoqqOqNop0pFek7FQ/GPc5t5ZdQZ34HcOQ46',
                'role' => 'admin',
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
                'password' => '$2y$12$dCO1DugwBlRsIRxYGJNUuO9sbxlzfz1QcDqsrpnocKjpUTJqrQIgq',
                'role' => 'petugas',
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
                'password' => '$2y$12$X84PPJvI.6ch/wcROqjVs.hxE0EjVS.ZQJuPA2xQPLFeTVPBxwAyu',
                'role' => 'owner',
                'remember_token' => 'udhXpAjD29',
                'created_at' => '2026-02-20 04:12:58',
                'updated_at' => '2026-02-20 04:12:58',
                'deleted_at' => null,
            ],
        ]);
    }
}

