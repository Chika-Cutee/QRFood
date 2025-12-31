<?php

namespace App\Http\Controllers;

use App\Models\Produk; // <-- Pastikan ini di-import
use Illuminate\Http\Request;

class UserDashboardController extends Controller
{
    /**
     * Menampilkan halaman menu/dashboard utama user.
     */
    public function index()
    {
        // Ambil SEMUA produk dari database
        $produks = Produk::all();
        
        // Kirim data 'produks' ke view
        return view('user.dashboard', compact('produks'));
    }

    /**
     * Method untuk Scan QR Meja
     */
    public function simpanMejaDanMulai(Request $request, $nomor)
    {
        // 1. Simpan nomor meja ke dalam Sesi
        $request->session()->put('nomor_meja', $nomor);

        // 2. Arahkan pelanggan ke halaman menu/dashboard utama
        return redirect()->route('user.dashboard');
    }
}