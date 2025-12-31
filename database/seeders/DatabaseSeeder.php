<?php

namespace Database\Seeders;

use App\Models\Admin; // <-- Tambahkan
use App\Models\Kasir; // <-- Tambahkan
use App\Models\Koki;  // <-- Tambahkan
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // <-- Tambahkan

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // User::factory(10)->create(); // Hapus atau biarkan jika perlu

        // Akun Admin Default
        Admin::firstOrCreate(
            ['email' => 'admin@gmail.com'], // Cek apakah email ini ada?
            [
                'name' => 'Admin Gerai',    // Jika tidak ada, buat baru dengan data ini
                'password' => Hash::make('123456789')
            ]
        );

        // Akun Kasir Default
        Kasir::firstOrCreate(
            ['email' => 'kasir@gmail.com'],
            [
                'name' => 'Kasir Gerai',
                'password' => Hash::make('123456789')
            ]
        );

        // Akun Koki Default (Hanya 1)
        Koki::firstOrCreate(
            ['email' => 'koki@gmail.com'],
            [
                'name' => 'Koki Gerai',
                'password' => Hash::make('123456789')
            ]
        );

        $this->call([
            ProdukSeeder::class,
        ]);
    }
}