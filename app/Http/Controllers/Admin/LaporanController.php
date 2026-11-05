<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kasir;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Menampilkan halaman daftar laporan.
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
     * Menampilkan halaman detail laporan.
     */
    public function show($kasir_id, $tanggal)
    {
        return view('admin.laporan.show', $this->siapkanDataLaporan($kasir_id, $tanggal));
    }

    /**
     * Menghapus laporan (semua transaksi selesai milik satu kasir pada satu tanggal).
     */
    public function destroy($kasir_id, $tanggal)
    {
        // [Efisiensi] Sebelumnya: ambil semua model (get) lalu delete satu per satu di dalam
        // foreach = 1 query SELECT + N query DELETE. Sekarang 1 query DELETE.
        // Detail ikut terhapus oleh onDelete('cascade') pada migrasi transaksi_details.
        Transaksi::where('status', 'selesai')
            ->where('kasir_id', $kasir_id)
            ->whereDate('created_at', $this->parseTanggal($tanggal))
            ->delete();

        return redirect()->route('admin.laporan.index')->with('success', 'Laporan berhasil dihapus.');
    }

    /**
     * Ekspor PDF.
     */
    public function exportPdf($kasir_id, $tanggal)
    {
        $data = $this->siapkanDataLaporan($kasir_id, $tanggal);

        $namaFile = 'Laporan - ' . $data['kasir']->name . ' - '
            . $this->parseTanggal($tanggal)->format('d-m-Y') . '.pdf';

        return Pdf::loadView('admin.laporan.pdf', $data)->download($namaFile);
    }

    public function showGrafik()
    {
        $tahunSaatIni = Carbon::now()->year;
        $tahunMulai   = $tahunSaatIni - 4; // 5 tahun terakhir

        // 1. Penjualan bulanan (tahun ini)
        $penjualanBulanan = Transaksi::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(total_harga) as total')
        )
            ->whereYear('created_at', $tahunSaatIni)
            ->where('status', 'selesai')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $dataBulan = array_fill(1, 12, 0);
        foreach ($penjualanBulanan as $data) {
            $dataBulan[$data->month] = $data->total;
        }

        // 2. Penjualan tahunan (5 tahun terakhir)
        $penjualanTahunan = Transaksi::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('SUM(total_harga) as total')
        )
            ->where('status', 'selesai')
            ->whereYear('created_at', '>=', $tahunMulai)
            ->groupBy('year')
            ->orderBy('year')
            ->get();

        return view('admin.grafik_penjualan', [
            'dataBulananJson' => json_encode(array_values($dataBulan)),
            'labelsTahun'     => json_encode($penjualanTahunan->pluck('year')),
            'dataTahunJson'   => json_encode($penjualanTahunan->pluck('total')),
            'tahunSaatIni'    => $tahunSaatIni,
        ]);
    }

    // ------------------------------------------------------------------
    //  Rutinitas bantu (private)
    // ------------------------------------------------------------------

    /**
     * [Komputasional - Duplikasi rutinitas] Query laporan sebelumnya ditulis dua kali
     * persis sama (show & exportPdf). Sekarang satu rutinitas yang dipakai bersama.
     */
    private function siapkanDataLaporan($kasir_id, $tanggal): array
    {
        $kasir = Kasir::find($kasir_id);

        if ($kasir === null) {
            $kasir       = new Kasir();
            $kasir->name = '[Kasir Telah Dihapus]';
            $kasir->id   = $kasir_id;
        }

        // [Efisiensi] pluck('id') + whereIn(array) = 2 query & array besar di RAM.
        // Diganti subquery -> 1 query, id tidak dimuat ke PHP.
        $idTransaksi = Transaksi::select('id')
            ->where('status', 'selesai')
            ->where('kasir_id', $kasir_id)
            ->whereDate('created_at', $this->parseTanggal($tanggal));

        $daftarPenjualan = TransaksiDetail::whereIn('transaksi_id', $idTransaksi)
            ->select(
                'nama_produk',
                'harga_satuan',
                DB::raw('SUM(jumlah) as total_jumlah'),
                DB::raw('SUM(subtotal) as total_subtotal')
            )
            ->groupBy('nama_produk', 'harga_satuan')
            ->get();

        return [
            'kasir'           => $kasir,
            'tanggal'         => $tanggal,
            'daftarPenjualan' => $daftarPenjualan,
            'totalPendapatan' => $daftarPenjualan->sum('total_subtotal'),
        ];
    }

    /**
     * [Pencegahan Cacat] Carbon::parse() melempar exception (HTTP 500) untuk tanggal
     * tidak valid di URL. Sekarang dijawab 404.
     */
    private function parseTanggal($tanggal): Carbon
    {
        try {
            return Carbon::parse($tanggal);
        } catch (\Exception $e) {
            abort(404, 'Format tanggal tidak valid.');
        }
    }
}
