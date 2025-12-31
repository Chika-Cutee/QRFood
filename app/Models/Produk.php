<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'category',
        'image_url',
    ];

    /**
     * ACCESSOR PINTAR:
     * Membedakan antara permintaan dari Android (API) dan Web Admin.
     */
    public function getImageUrlAttribute($value)
    {
        if (!$value) return null;

        // 1. Jika sudah link internet (https://...), biarkan saja
        if (str_contains($value, 'http')) {
            return $value;
        }

        // 2. Bersihkan slash depan
        if (str_starts_with($value, '/')) {
            $value = substr($value, 1);
        }

        // === LOGIKA BARU ===
        
        // Cek 1: Apakah permintaan datang dari API (Android)?
        // Cek 2: ATAU apakah kita sedang dalam proses Midtrans (biasanya URL-nya mengandung midtrans)?
        if (request()->is('api/*')) {
            // Android butuh URL Lengkap (Ngrok)
            return config('app.url') . '/' . $value;
        }

        // Jika dibuka dari Web Admin (Laptop), kembalikan path aslinya
        // Biarkan blade view menggunakan asset() secara manual
        return $value;
    }
}