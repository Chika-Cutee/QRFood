<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kasir_id',
        'nama_pemesan',
        'nomor_meja',
        'total_harga',
        'metode_pembayaran',
        'status',
        'catatan',
    ];

    /**
     * Mendapatkan detail (item) dari transaksi ini.
     */
    public function details()
    {
        return $this->hasMany(TransaksiDetail::class);
    }

    /**
     * ▼▼▼ TAMBAHKAN FUNGSI INI ▼▼▼
     * Mendapatkan data kasir yang menangani transaksi ini.
     */
    public function kasir()
    {
        // Transaksi ini "milik" satu Kasir
        return $this->belongsTo(Kasir::class);
    }
}