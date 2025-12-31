<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id(); // ID Transaksi (Contoh: BON-001)

            // Siapa yang pesan? (Terhubung ke tabel 'users')
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Siapa kasir yang proses? (Bisa null jika belum diproses)
            $table->foreignId('kasir_id')->nullable()->constrained('kasirs')->onDelete('set null');

            $table->string('nama_pemesan'); // Kita salin nama user-nya
            $table->integer('nomor_meja');
            $table->integer('total_harga');
            $table->string('metode_pembayaran'); // 'CASH' atau 'BRI'
            
            // Ini KUNCI untuk progress bar Anda
            $table->string('status')->default('menunggu_pembayaran'); 
            // Pilihan: 'menunggu_pembayaran', 'diproses', 'selesai', 'dibatalkan'
            
            $table->timestamps(); // Tanggal transaksi dibuat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};