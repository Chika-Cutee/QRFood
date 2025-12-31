<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Produk; // <-- TAMBAHKAN INI

class ProdukSeeder extends Seeder
{
    public function run(): void
    {
        // Data Makanan
        Produk::create([
            'name' => 'Ayam Penyet',
            'price' => 23000,
            'category' => 'makanan',
            'image_url' => 'https://placehold.co/100x100/F9E4C8/333?text=Ayam+Penyet'
        ]);
        Produk::create([
            'name' => 'Ayam Geprek',
            'price' => 25000,
            'category' => 'makanan',
            'image_url' => 'https://placehold.co/100x100/F9E4C8/333?text=Ayam+Geprek'
        ]);
        Produk::create([
            'name' => 'Ayam Bakar',
            'price' => 25000,
            'category' => 'makanan',
            'image_url' => 'https://placehold.co/100x100/F9E4C8/333?text=Ayam+Bakar'
        ]);

        // Data Minuman
        Produk::create([
            'name' => 'Jus Jeruk',
            'price' => 7000,
            'category' => 'minuman',
            'image_url' => 'https://placehold.co/100x100/C8F9E4/333?text=Jus+Jeruk'
        ]);
        Produk::create([
            'name' => 'Teh Es',
            'price' => 5000,
            'category' => 'minuman',
            'image_url' => 'https://placehold.co/100x100/C8F9E4/333?text=Teh+Es'
        ]);

        // Data Paket
        Produk::create([
            'name' => 'Nila Bakar + Es Teh',
            'price' => 35000,
            'category' => 'paket',
            'image_url' => 'https://placehold.co/100x100/E4C8F9/333?text=Paket+Nila'
        ]);
    }
}