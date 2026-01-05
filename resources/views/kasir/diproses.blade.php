@extends('layouts.kasir')

@section('title', 'Pesanan yang Diproses')
@section('header_title', 'Pesanan yang Diproses')

@push('styles')
<style>
    /* ... (CSS header & card sama) ... */
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(450px, 1fr)); /* Sedikit lebih lebar */
        gap: 1.5rem;
    }
    .order-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        overflow: hidden;
        display: flex; /* Aktifkan flexbox */
        flex-direction: column; /* Susun item secara vertikal */
    }
    .order-header {
        padding: 1rem;
        border-bottom: 1px solid #eee;
        background: #f9f9f9;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.5rem;
    }
    .order-header p { margin: 0; font-size: 0.95rem; }
    .order-header p strong { color: #333; }
    .order-body {
        padding: 1rem;
        flex-grow: 1; /* Biarkan body tumbuh dan mendorong footer */
    }
    .order-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 1rem;
    }
    
    /* --- ▼▼▼ PERBAIKAN CSS DI SINI ▼▼▼ --- */
    .order-table th, .order-table td {
        padding: 8px;
        text-align: center; /* 2. Tengahkan semua teks */
        border-bottom: 1px solid #eee;
    }
    .order-table th {
        background-color: #B91C1C;
        color: white;
    }
    /* Atur lebar kolom agar tidak terpotong */
    .order-table th:nth-child(1), .order-table td:nth-child(1) { 
        width: 40%; 
        text-align: left; /* Menu lebih baik rata kiri */
    } 
    .order-table th:nth-child(2), .order-table td:nth-child(2) { 
        width: 15%; 
    } /* Jumlah */
    .order-table th:nth-child(3), .order-table td:nth-child(3) { 
        width: 25%; 
        white-space: nowrap; /* 1. Cegah harga terpotong */
    } /* Harga */
    .order-table th:nth-child(4), .order-table td:nth-child(4) { 
        width: 20%; 
        white-space: nowrap; /* 1. Cegah total terpotong */
    } /* Total */

    .order-table .total-row {
        font-weight: bold;
        font-size: 1.1rem;
    }
    .order-table .total-row td {
        border-bottom: none;
        text-align: right; /* Total Harga tetap rata kanan */
        padding: 12px 8px; 
    }
    /* --- ▲▲▲ AKHIR PERBAIKAN ▲▲▲ --- */

    .order-footer {
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #eee; /* Tambah garis pemisah */
        background-color: #f9f9f9;
    }
    .status-text {
        font-weight: bold;
        font-size: 1rem;
        color: #16A34A; 
    }
    .btn-selesai {
        background-color: #B91C1C;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
    }
    .no-orders {
        text-align: center;
        padding: 2rem;
        color: #777;
    }
</style>
@endpush

@section('content')

    @if (session('success'))
        <div style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid-container">
        @forelse ($transaksis_diproses as $transaksi)
            <div class="order-card">
                <div class="order-header">
                    <p><strong>{{ $transaksi->nama_pemesan }}</strong></p>
                    <p><strong>Meja {{ $transaksi->nomor_meja == 0 ? '??' : $transaksi->nomor_meja }}</strong></p>
                    <p>{{ $transaksi->created_at->format('d/m/Y, H.i \W\I\B') }}</p>
                    <p>Metode: <strong>{{ $transaksi->metode_pembayaran }}</strong></p>
                </div>
                <div class="order-body">
                    <table class="order-table">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Jumlah</th>
                                <th>Harga Satuan</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($transaksi->details as $item)
                            <tr>
                                <td>{{ $item->nama_produk }}</td>
                                <td>{{ $item->jumlah }}</td>
                                <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            
                            <tr class="total-row">
                                <!-- Hapus inline style, biarkan CSS yang urus -->
                                <td colspan="4">
                                    Total Harga: Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @if($transaksi->catatan)
                    <div style="padding: 0 1rem 1rem; margin-top: -1rem;">
                        <p style="margin: 0; font-size: 0.95rem;"><strong>Catatan Pesanan:</strong></p>
                        <p style="margin: 0; font-size: 0.95rem; white-space: normal; word-wrap: break-word;">{{ $transaksi->catatan }}</p>
                    </div>
                    @endif
                </div>
                <div class="order-footer">
                    @if($transaksi->status == 'siap_saji')
                        <!-- Status Hijau: Koki sudah selesai -->
                        <span class="status-text" style="color: #16A34A;">
                            ✔ Siap Disajikan / Diantar
                        </span>
                        <form action="{{ route('kasir.selesai_pesanan', $transaksi->id) }}" method="POST" onsubmit="return confirm('Pesanan sudah diterima pelanggan? Pindahkan ke riwayat.')">
                            @csrf
                            <button type="submit" class="btn-selesai">
                                Tandai Selesai
                            </button>
                        </form>
                    @else
                        <!-- Status Orange: Masih dimasak -->
                        <span class="status-text" style="color: #d97706;">
                            ⏳ Sedang Dimasak Koki
                        </span>
                        <button type="button" class="btn-secondary" disabled style="background-color: #ccc; cursor: not-allowed;">
                            Menunggu Koki
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <p class="no-orders">Tidak ada pesanan yang sedang diproses.</p>
        @endforelse
    </div>
@endsection

@push('scripts')
<script>
    // Refresh halaman setiap 30 detik untuk update status dari Koki
    setTimeout(function(){
       window.location.reload();
    }, 30000);
</script>
@endpush