<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController; 
use App\Http\Controllers\Admin\ProdukController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\KasirController;
use App\Http\Controllers\PesananController; 
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Koki\DashboardController as KokiDashboardController;


// Rute Khusus untuk melihat Landing Page
Route::get('/landingpage', function () {
    return view('landing-page');
});

// Rute untuk Tamu (Guest) - (Login & Register)
Route::middleware('guest')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');

    // ALUR LUPA PASSWORD
    Route::get('/lupa-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])
         ->name('password.request');
    Route::post('/lupa-password', [ForgotPasswordController::class, 'sendResetLink'])
         ->name('password.email');
    Route::get('/verifikasi-otp', [ForgotPasswordController::class, 'showOtpForm'])
         ->name('password.otp.form');
    Route::post('/verifikasi-otp', [ForgotPasswordController::class, 'verifyOtp'])
         ->name('password.otp.verify');
    Route::get('/resend-otp', [ForgotPasswordController::class, 'resendOtp'])
         ->name('password.resend');
    Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])
         ->name('password.reset.form');
    Route::post('/reset-password', [ForgotPasswordController::class, 'updatePassword'])
         ->name('password.update');
    Route::get('/reset-sukses', [ForgotPasswordController::class, 'showSuccessPage'])
         ->name('password.success');
    
});

// Rute Logout
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth:web,admin,kasir,koki');

// RUTE SCAN MEJA
Route::get('/meja/{nomor}', [UserDashboardController::class, 'simpanMejaDanMulai'])
    ->name('scan.meja');

// Rute Dashboard (Perlu Login)

// Dashboard Admin
Route::middleware('auth:admin')->prefix('admin')->group(function () {
    Route::get('/menu', [ProdukController::class, 'index'])->name('admin.menu.index');
    Route::get('/menu/create', [ProdukController::class, 'create'])->name('admin.menu.create');
    Route::post('/menu', [ProdukController::class, 'store'])->name('admin.menu.store');
    Route::get('/menu/{produk}/edit', [ProdukController::class, 'edit'])->name('admin.menu.edit');
    Route::put('/menu/{produk}', [ProdukController::class, 'update'])->name('admin.menu.update');
    Route::delete('/menu/{produk}', [ProdukController::class, 'destroy'])->name('admin.menu.destroy');
    Route::get('/kasir', [KasirController::class, 'index'])->name('admin.kasir.index');
    Route::get('/kasir/create', [KasirController::class, 'create'])->name('admin.kasir.create');
    Route::post('/kasir', [KasirController::class, 'store'])->name('admin.kasir.store');
    Route::get('/kasir/{kasir}/edit', [KasirController::class, 'edit'])->name('admin.kasir.edit');
    Route::put('/kasir/{kasir}', [KasirController::class, 'update'])->name('admin.kasir.update');
    Route::delete('/kasir/{kasir}', [KasirController::class, 'destroy'])->name('admin.kasir.destroy');

    
    // RUTE UNTUK LAPORAN PENJUALAN
    Route::get('/laporan', [LaporanController::class, 'index'])->name('admin.laporan.index');
    // Rutenya sekarang pakai kasir_id DAN tanggal
    Route::get('/laporan/{kasir_id}/{tanggal}', [LaporanController::class, 'show'])->name('admin.laporan.show');
    Route::delete('/laporan/{kasir_id}/{tanggal}', [LaporanController::class, 'destroy'])->name('admin.laporan.destroy');
    Route::get('/laporan/pdf/{kasir_id}/{tanggal}', [LaporanController::class, 'exportPdf'])->name('admin.laporan.pdf');
    // ▲▲▲ AKHIR PERUBAHAN ▲▲▲
    
    // Route untuk Laporan Harian (index)
    Route::get('/', [LaporanController::class, 'index'])->name('index'); 

    // Route BARU untuk Grafik
    Route::get('/grafik', [LaporanController::class, 'showGrafik'])->name('admin.laporan.grafik');
});

// Dashboard Kasir
Route::middleware('auth:kasir')->prefix('kasir')->name('kasir.')->group(function () {
    // Halaman utama (Pemesanan pelanggan)
    Route::get('/dashboard', [KasirDashboardController::class, 'index'])->name('dashboard');
    // Rute untuk tombol "Konfirmasi pesanan"
    Route::post('/konfirmasi/{transaksi}', [KasirDashboardController::class, 'konfirmasi'])->name('konfirmasi');
    // Rute untuk halaman "Pesanan yang diproses"
    Route::get('/diproses', [KasirDashboardController::class, 'showPesananDiproses'])->name('diproses');
    // Rute untuk tombol "Tandai Selesai"
    Route::post('/selesai/{transaksi}', [KasirDashboardController::class, 'tandaiSelesai'])->name('selesai');
    // Rute untuk halaman "Riwayat pemesanan"
    Route::get('/riwayat', [KasirDashboardController::class, 'showRiwayat'])->name('riwayat');
    // ▼▼▼ RUTE BARU UNTUK HAPUS ▼▼▼
    Route::delete('/riwayat/hapus-semua', [KasirDashboardController::class, 'destroyAll'])->name('riwayat.destroyAll');
    Route::delete('/riwayat/{transaksi}', [KasirDashboardController::class, 'destroy'])->name('riwayat.destroy');
    // ▲▲▲ --------------------- ▲▲▲
    // Perbaiki route ini agar mengarah ke KasirDashboardController yang benar
    Route::post('/selesai-pesanan/{transaksi}', [KasirDashboardController::class, 'tandaiSelesai'])->name('selesai_pesanan');
});

/// Dashboard User (Pelanggan)
Route::middleware('auth:web')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    Route::post('/pesanan/hitung', [PesananController::class, 'hitungPesanan'])->name('pesanan.hitung');
    Route::get('/pesanan/konfirmasi', [PesananController::class, 'showKonfirmasi'])->name('pesanan.konfirmasi.show');
    Route::post('/pesanan/buat', [PesananController::class, 'buatPesanan'])->name('pesanan.buat');
    Route::get('/pesanan/sukses/cash/{transaksi}', [PesananController::class, 'showSuksesCash'])->name('pesanan.sukses.cash');
    
    // ▼▼▼ RUTE MIDTRANS ▼▼▼
    
    // Halaman Pembayaran (Popup Snap)
    Route::get('/pesanan/bayar/{transaksi}', [PesananController::class, 'showBayar'])->name('pesanan.bayar');
    
    // Halaman Sukses Midtrans (SUDAH BENAR KE PesananController)
    Route::get('/pesanan/sukses/midtrans/{transaksi}', [PesananController::class, 'showSuksesMidtrans'])->name('pesanan.sukses.midtrans');
    
    // 1. Halaman Pending (GANTI ke PesananController)
    Route::get('/order/pending/{id}', [PesananController::class, 'pendingPage'])->name('order.pending');

    // 2. Halaman Cancel (PISAHKAN dari komentar agar jalan)
    Route::get('/order/cancel/{id}', [PesananController::class, 'cancelOrder'])->name('order.cancel');

    // 3. Halaman Success/Selesai (GANTI ke PesananController)
    Route::get('/order/success/{id}', [PesananController::class, 'successPage'])->name('order.success');


    // --- TAMBAHKAN INI UNTUK LANDING PAGE ---

     // 1. Route untuk menampilkan Landing Page
     // Pengunjung web bisa mengaksesnya via: namadomain.com/info
     Route::get('/info', function () {
     return view('landingpage'); // Sesuaikan dengan nama file blade landing page Anda
     })->name('landing.index');

     // 2. Route untuk memproses kirim ulasan (POST)
     // Ini diperlukan agar form ulasan di landing page bisa berfungsi
     Route::post('/info/ulasan', [LandingPageController::class, 'storeUlasan'])->name('ulasan.store');
});

     // Route Group untuk Koki
     Route::prefix('koki')->name('koki.')->middleware('auth:koki')->group(function () {
     Route::get('/dashboard', [KokiDashboardController::class, 'index'])->name('dashboard');
     Route::patch('/pesanan/{transaksi}/selesai', [KokiDashboardController::class, 'tandaiSelesai'])->name('selesai');
});