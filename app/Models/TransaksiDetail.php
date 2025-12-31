<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiDetail extends Model
{
    use HasFactory;

    // ▼▼▼ TAMBAHKAN INI ▼▼▼
    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'nama_produk',
        'harga_satuan',
        'jumlah',
        'subtotal',
        'image_url',
    ];
    public function produk()
    {
        // Pastikan Anda memiliki Model Produk.php
        return $this->belongsTo(Produk::class, 'produk_id');
    }

}