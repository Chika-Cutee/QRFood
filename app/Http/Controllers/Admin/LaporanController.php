<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kasir;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf; // <-- 1. IMPORT PDF FACADE

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman daftar laporan (Gambar 1)
     */
    public function index()
    {
        $laporan_grup = Transaksi::where('status', 'selesai')
            ->with('kasir') 
            ->select(
                'kasir_id', 
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('COUNT(id) as jumlah_transaksi')
            )
            ->groupBy('kasir_id', 'tanggal')
            ->orderBy('tanggal', 'desc')
            ->orderBy('kasir_id', 'asc')
            ->get();
        
        return view('admin.laporan.index', compact('laporan_grup'));
    }

    /**
     * Menampilkan halaman detail laporan (Gambar 2)
     */
    public function show($kasir_id, $tanggal)
    {
        $kasir = Kasir::find($kasir_id);
        if (!$kasir) {
            $kasir = new Kasir();
            $kasir->name = '[Kasir Telah Dihapus]';
            $kasir->id = $kasir_id;
        }

        $parsedDate = Carbon::parse($tanggal);

        $transaksiIds = Transaksi::where('status', 'selesai')
            ->where('kasir_id', $kasir_id)
            ->whereDate('created_at', $parsedDate)
            ->pluck('id'); 

        $daftarPenjualan = TransaksiDetail::whereIn('transaksi_id', $transaksiIds)
            ->select(
                'nama_produk', 
                'harga_satuan', 
                DB::raw('SUM(jumlah) as total_jumlah'), 
                DB::raw('SUM(subtotal) as total_subtotal')
            )
            ->groupBy('nama_produk', 'harga_satuan')
            ->get();

        $totalPendapatan = $daftarPenjualan->sum('total_subtotal');

        return view('admin.laporan.show', compact(
            'kasir', 
            'tanggal',
            'daftarPenjualan', 
            'totalPendapatan'
        ));
    }

    /**
     * Menghapus Laporan (Tambahan Anda)
     */
    public function destroy($kasir_id, $tanggal)
    {
        $parsedDate = Carbon::parse($tanggal);

        $transaksis = Transaksi::where('status', 'selesai')
            ->where('kasir_id', $kasir_id)
            ->whereDate('created_at', $parsedDate)
            ->get();
        
        foreach($transaksis as $transaksi) {
            $transaksi->delete(); 
        }

        return redirect()->route('admin.laporan.index')->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * ▼▼▼ FUNGSI INI SEKARANG DIISI ▼▼▼
     * Ekspor PDF
     */
    public function exportPdf($kasir_id, $tanggal)
    {
        // 1. Ambil data (Logika yang SAMA PERSIS seperti method 'show')
        $kasir = Kasir::find($kasir_id);
        if (!$kasir) {
            $kasir = new Kasir();
            $kasir->name = '[Kasir Telah Dihapus]';
            $kasir->id = $kasir_id;
        }

        $parsedDate = Carbon::parse($tanggal);

        $transaksiIds = Transaksi::where('status', 'selesai')
            ->where('kasir_id', $kasir_id)
            ->whereDate('created_at', $parsedDate)
            ->pluck('id');

        $daftarPenjualan = TransaksiDetail::whereIn('transaksi_id', $transaksiIds)
            ->select(
                'nama_produk', 
                'harga_satuan', 
                DB::raw('SUM(jumlah) as total_jumlah'),
                DB::raw('SUM(subtotal) as total_subtotal')
            )
            ->groupBy('nama_produk', 'harga_satuan')
            ->get();

        $totalPendapatan = $daftarPenjualan->sum('total_subtotal');

        // 2. Siapkan data untuk dikirim ke view PDF
        $data = [
            'kasir' => $kasir,
            'tanggal' => $tanggal, // Kirim tanggal (string)
            'daftarPenjualan' => $daftarPenjualan,
            'totalPendapatan' => $totalPendapatan
        ];

        // 3. Buat nama file
        $namaFile = 'Laporan - ' . $kasir->name . ' - ' . $parsedDate->format('d-m-Y') . '.pdf';

        // 4. Load view PDF ('admin.laporan.pdf') dan data, lalu unduh
        $pdf = Pdf::loadView('admin.laporan.pdf', $data);
        return $pdf->download($namaFile);
    }

    public function showGrafik()
    {
        // 1. Data Penjualan Bulanan (Total Penjualan per Bulan)
        $penjualanBulanan = Transaksi::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_harga) as total')
        )
        ->whereYear('created_at', Carbon::now()->year) // Hanya ambil tahun ini
        ->where('status', 'selesai') // Ubah ke 'selesai' agar sama dengan Laporan Harian
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Format data bulanan untuk ChartJS (0 = Kosong)
        $dataBulan = array_fill(1, 12, 0);
        foreach ($penjualanBulanan as $data) {
            $dataBulan[$data->month] = $data->total;
        }
        $dataBulananJson = json_encode(array_values($dataBulan));

        // 2. Data Penjualan Tahunan (Total Penjualan 5 Tahun Terakhir)
        $tahunSaatIni = Carbon::now()->year;
        $tahunMulai = $tahunSaatIni - 4; // Ambil 5 tahun terakhir

        $penjualanTahunan = Transaksi::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total_harga) as total')
        )
        ->where('status', 'selesai') // Ubah ke 'selesai'
        ->whereYear('created_at', '>=', $tahunMulai)
        ->groupBy('year')
        ->orderBy('year')
        ->get();

        $labelsTahun = $penjualanTahunan->pluck('year');
        $dataTahun = $penjualanTahunan->pluck('total');

        return view('admin.grafik_penjualan', [
            'dataBulananJson' => $dataBulananJson,
            'labelsTahun' => json_encode($labelsTahun),
            'dataTahunJson' => json_encode($dataTahun),
            'tahunSaatIni' => $tahunSaatIni,
        ]);
    }
}