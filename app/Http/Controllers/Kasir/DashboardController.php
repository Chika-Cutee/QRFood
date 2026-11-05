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
        // [Pencegahan Cacat - if/else] Sebelumnya status tidak dicek: pesanan yang sudah
        // diproses/selesai/batal bisa "dikonfirmasi" ulang. Sekarang hanya status yang valid.
        if (in_array($transaksi->status, ['menunggu_pembayaran', 'dibayar'], true)) {
            $transaksi->update([
                'status'   => 'diproses',
                'kasir_id' => Auth::guard('kasir')->id(),
            ]);

            $response = redirect()->route('kasir.dashboard')
                ->with('success', 'Pesanan #' . $transaksi->id . ' berhasil dikonfirmasi.');
        } else {
            $response = redirect()->route('kasir.dashboard')
                ->with('error', 'Pesanan #' . $transaksi->id . ' tidak dapat dikonfirmasi.');
        }

        return $response;
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
        $search       = trim((string) $request->input('search'));
        $queryBuilder = Transaksi::where('status', 'selesai');

        if ($search !== '') {
            // Cari di TIGA tempat: nama, meja, atau tanggal (d/m/Y)
            $tanggal = $this->parseTanggalPencarian($search);

            $queryBuilder->where(function ($q) use ($search, $tanggal) {
                $q->where('nama_pemesan', 'LIKE', "%{$search}%")
                  ->orWhere('nomor_meja', $search);

                if ($tanggal !== null) {
                    $q->orWhereDate('created_at', $tanggal);
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
     * [Pencegahan Cacat] Exception tidak lagi dipakai sebagai alur kontrol normal.
     * Mengembalikan 'Y-m-d' bila $teks bertanggal d/m/Y yang valid, selain itu null.
     */
    private function parseTanggalPencarian(string $teks): ?string
    {
        if (!Carbon::canBeCreatedFromFormat($teks, 'd/m/Y')) {
            return null;
        }

        return Carbon::createFromFormat('d/m/Y', $teks)->format('Y-m-d');
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