@extends('layouts.admin')

@section('title', 'Detail Laporan')
@section('header_title', 'Laporan Penjualan Kasir')

@push('styles')
<style>
    .laporan-header {
        margin-bottom: 1.5rem;
    }
    .laporan-header p {
        font-size: 1.1rem;
        font-weight: 600;
        margin: 0.5rem 0;
    }
    .laporan-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1.5rem;
    }
    .laporan-table th, .laporan-table td {
        padding: 10px;
        text-align: center;
        border: 1px solid #000; /* Border hitam sesuai desain */
    }
    .laporan-table th {
        background-color: #B91C1C;
        color: white;
        font-size: 1.1rem;
    }
    .laporan-table td {
        font-size: 1rem;
    }
    /* Kolom Nama Menu rata kiri */
    .laporan-table th:nth-child(2), .laporan-table td:nth-child(2) {
        text-align: left;
    }
    .total-pendapatan {
        font-size: 1.25rem;
        font-weight: bold;
        text-align: right;
        margin-bottom: 2rem;
    }
    .action-buttons {
        display: flex;
        justify-content: flex-end; /* Tombol di kanan */
        gap: 1rem;
    }
    .btn-action {
        padding: 10px 25px;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: bold;
        color: white;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-kembali {
        background-color: #6c757d; /* Abu-abu */
    }
    .btn-pdf {
        background-color: #B91C1C; /* Merah */
    }
    .btn-hapus {
        background-color: #DC3545; /* Merah (Hapus) */
    }
</style>
@endpush

@section('content')

    <!-- Info Laporan -->
    <div class="laporan-header">
        <p>Nama kasir: {{ $kasir->name }}</p>
        <p>Tanggal: {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</p>
    </div>

    <h3 style="font-size: 1.2rem; font-weight: bold;">Daftar Penjualan hari ini</h3>
    
    <!-- Tabel Item (Digabung/Summary) -->
    <table class="laporan-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Menu</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($daftarPenjualan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->nama_produk }}</td>
                <td>{{ $item->total_jumlah }}</td> <!-- total_jumlah (dari SUM) -->
                <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($item->total_subtotal, 0, ',', '.') }}</td> <!-- total_subtotal (dari SUM) -->
            </tr>
            @empty
            <tr>
                <td colspan="5">Tidak ada penjualan untuk laporan ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Total Pendapatan -->
    <div class="total-pendapatan">
        Total Pendapatan Hari Ini
        <span style="display: block; margin-top: 0.5rem; color: #B91C1C;">
            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
        </span>
    </div>

    <!-- Tombol Aksi (Bawah) -->
    <div class="action-buttons">
        <!-- Tombol Hapus (Tambahan Anda) -->
        <form action="{{ route('admin.laporan.destroy', ['kasir_id' => $kasir->id, 'tanggal' => $tanggal]) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus laporan ini? Semua data transaksi kasir ini di tanggal ini akan hilang.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-action btn-hapus">Hapus</button>
        </form>
    
        <!-- Tombol PDF -->
        <a href="{{ route('admin.laporan.pdf', ['kasir_id' => $kasir->id, 'tanggal' => $tanggal]) }}" class="btn-action btn-pdf">Ekspor PDF</a>
        
        <!-- Tombol Kembali -->
        <a href="{{ route('admin.laporan.index') }}" class="btn-action btn-kembali">Kembali</a>
    </div>

@endsection