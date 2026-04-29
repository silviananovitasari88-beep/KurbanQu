<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Kos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat admin user default (jika belum ada)
        User::firstOrCreate(
            ['email' => 'admin'],
            [
                'name' => 'Admin',
                'password' => Hash::make('admin'),
                'no_hp' => '-',
            ]
        );

        // Seed 16 kamar default seperti di kosan 2
        // House 105: rooms 1-10
        for ($i = 1; $i <= 10; $i++) {
            $number = '105-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            Kos::firstOrCreate(
                ['number' => $number],
                [
                    'harga' => 500000,
                    'status' => 'tersedia',
                    'penyewa' => null,
                    'foto' => null,
                ]
            );
        }

        // House 121: rooms 1-6
        for ($i = 1; $i <= 6; $i++) {
            $number = '121-' . str_pad($i, 2, '0', STR_PAD_LEFT);
            Kos::firstOrCreate(
                ['number' => $number],
                [
                    'harga' => 600000,
                    'status' => 'tersedia',
                    'penyewa' => null,
                    'foto' => null,
                ]
            );
        }
    }
}
