<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // <-- TAMBAHKAN IMPORT CARBON

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman "Pemesanan pelanggan"
     */
    public function index()
    {
        $transaksis = Transaksi::whereIn('status', ['menunggu_pembayaran', 'dibayar'])
                                ->with('details')
                                ->orderBy('created_at', 'asc') 
                                ->get();

        return view('kasir.dashboard', compact('transaksis'));
    }

    /**
     * Mengkonfirmasi pesanan (Tombol "Konfirmasi pesanan")
     */
    public function konfirmasi(Transaksi $transaksi)
    {
        $kasirId = Auth::guard('kasir')->id();

        $transaksi->update([
            'status' => 'diproses',
            'kasir_id' => $kasirId
        ]);
        
        return redirect()->route('kasir.dashboard')->with('success', 'Pesanan #' . $transaksi->id . ' berhasil dikonfirmasi.');
    }

    /**
     * Menampilkan halaman "Pesanan yang diproses"
     */
    public function showPesananDiproses()
    {
        $kasirId = Auth::guard('kasir')->id();

        // Tampilkan yang sedang dimasak (diproses) ATAU yang sudah siap (siap_saji)
        $transaksis_diproses = Transaksi::whereIn('status', ['diproses', 'siap_saji'])
                                ->where('kasir_id', $kasirId) 
                                ->with('details')
                                ->orderBy('updated_at', 'asc') 
                                ->get();
        
        return view('kasir.diproses', compact('transaksis_diproses'));
    }

    /**
     * Tombol "Tandai Selesai"
     */
    public function tandaiSelesai(Transaksi $transaksi)
    {
        // Pastikan statusnya sudah 'siap_saji' sebelum diselesaikan
        if ($transaksi->status == 'siap_saji') {
            $transaksi->update(['status' => 'selesai']);
            return redirect()->back()->with('success', 'Pesanan selesai dan masuk riwayat.');
        }

        return redirect()->back()
            ->with('error', 'Pesanan belum siap disajikan oleh Koki.');
    }

    /**
     * Menampilkan halaman "Riwayat pemesanan"
     */
    public function showRiwayat(Request $request)
    {
        // 1. Ambil input pencarian
        $search = $request->input('search');

        // 2. Mulai query ke tabel transaksi
        $queryBuilder = Transaksi::where('status', 'selesai');

        // 3. Jika ada input pencarian (tidak kosong)
        if ($search) {
            // Kita akan mencari di TIGA tempat: nama, meja, atau tanggal
            $queryBuilder->where(function ($q) use ($search) {
                
                // A. Cari berdasarkan Nama Pemesan
                $q->where('nama_pemesan', 'LIKE', "%{$search}%");
                
                // B. Cari berdasarkan Nomor Meja
                $q->orWhere('nomor_meja', $search);

                // C. Cari berdasarkan Tanggal (coba format d/m/Y)
                try {
                    // Coba ubah "12/11/2025" menjadi "2025-11-12"
                    $formattedDate = Carbon::createFromFormat('d/m/Y', $search)->format('Y-m-d');
                    // Cari di database yang tanggalnya sama
                    $q->orWhereDate('created_at', $formattedDate);
                } catch (\Exception $e) {
                    // Jika formatnya salah (misal: "Flora"), biarkan saja
                }
            });
        }

        // 4. Ambil hasilnya
        $transaksis_selesai = $queryBuilder->with('details')
                                ->orderBy('updated_at', 'desc')
                                ->get();
        
        return view('kasir.riwayat', compact('transaksis_selesai'));
    }
    /**
     * (BARU) Menghapus SATU riwayat transaksi
     */
    public function destroy(Transaksi $transaksi)
    {
        // Pastikan hanya status 'selesai' yang bisa dihapus di sini
        if ($transaksi->status !== 'selesai') {
            return back()->with('error', 'Hanya pesanan yang sudah selesai yang bisa dihapus.');
        }

        $transaksi->delete();

        return back()->with('success', 'Riwayat pesanan berhasil dihapus.');
    }

    /**
     * (BARU) Menghapus SEMUA riwayat transaksi
     */
    public function destroyAll()
    {
        // Hapus semua yang statusnya 'selesai'
        Transaksi::where('status', 'selesai')->delete();

        return back()->with('success', 'Semua riwayat pesanan berhasil dihapus.');
    }
}