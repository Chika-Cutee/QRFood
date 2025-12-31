<?php

namespace App\Http\Controllers\Koki;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman pesanan yang perlu dimasak (Status: diproses)
     */
    public function index()
    {
        // Koki hanya melihat pesanan yang statusnya 'diproses' (sudah dikonfirmasi kasir)
        $transaksis = Transaksi::where('status', 'diproses')
                                ->with('details')
                                ->orderBy('updated_at', 'asc') // Yang duluan dikonfirmasi tampil di atas
                                ->get();

        return view('koki.dashboard', compact('transaksis'));
    }

    /**
     * Menandai pesanan selesai (Siap disajikan)
     */
    public function tandaiSelesai(Transaksi $transaksi)
    {
        // Pastikan statusnya memang diproses
        if ($transaksi->status !== 'diproses') {
            return back()->with('error', 'Pesanan tidak valid untuk diselesaikan.');
        }

        // Update status menjadi 'siap_saji' (Bukan selesai)
        $transaksi->update([
            'status' => 'siap_saji'
        ]);

        return redirect()->route('koki.dashboard')->with('success', 'Pesanan #' . $transaksi->id . ' selesai dimasak dan siap disajikan.');
    }
}
