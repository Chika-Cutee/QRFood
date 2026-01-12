@extends('layouts.kasir')

@section('title', 'Pemesanan Pelanggan')
@section('header_title', 'Pemesanan Pelanggan')

@push('styles')
<style>
    .grid-container {
        /* display: grid; Dihapus agar default satu kolom */
        gap: 1.5rem;
    }
    /* Terapkan grid hanya di layar lebar */
    @media (min-width: 500px) {
        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
        }
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
    .order-table th, .order-table td {
        padding: 8px;
        text-align: center;
        border-bottom: 1px solid #eee;
    }
    .order-table th { background-color: #B91C1C; color: white; }
    .order-table th:nth-child(1), .order-table td:nth-child(1) { width: 40%; text-align: left; } 
    .order-table th:nth-child(2), .order-table td:nth-child(2) { width: 15%; } 
    .order-table th:nth-child(3), .order-table td:nth-child(3) { width: 25%; white-space: nowrap; } 
    .order-table th:nth-child(4), .order-table td:nth-child(4) { width: 20%; white-space: nowrap; } 

    .order-table .total-row td {
        border-bottom: none;
        text-align: right;
        padding: 12px 8px;
        font-weight: bold;
        font-size: 1.1rem;
    }

    .order-footer {
        padding: 1rem; /* Ubah padding agar konsisten */
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid #eee; /* Tambah garis pemisah */
        background-color: #f9f9f9;
    }
    .btn-konfirmasi {
        background-color: #B91C1C;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
    }
    .no-orders { text-align: center; padding: 2rem; color: #777; }

    /* LABEL STATUS PEMBAYARAN */
    .badge-status {
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.9rem;
        font-weight: bold;
    }
    .badge-lunas {
        background-color: #D1FAE5;
        color: #065F46;
        border: 1px solid #34D399;
    }
    .badge-belum-cash {
        background-color: #FEE2E2;
        color: #991B1B;
        border: 1px solid #FCA5A5;
    }
    .badge-belum-midtrans {
        background-color: #FEF3C7; /* Kuning */
        color: #92400E;
        border: 1px solid #F59E0B;
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
        @forelse ($transaksis as $transaksi)
            <div class="order-card">
                <div class="order-header">
                    <p><strong>{{ $transaksi->nama_pemesan }}</strong></p>
                    <p><strong>Meja {{ $transaksi->nomor_meja == 0 ? '??' : $transaksi->nomor_meja }}</strong></p>
                    <p>{{ $transaksi->created_at->format('d/m/Y, H.i \W\I\B') }}</p>
                    <!-- Menampilkan Metode Pembayaran -->
                    <p>Metode: 
                        <strong>
                            @if($transaksi->metode_pembayaran == 'MIDTRANS')
                                Transfer / QRIS
                            @else
                                CASH
                            @endif
                        </strong>
                    </p>
                </div>
                <div class="order-body">
                    <div style="overflow-x: auto; width: 100%;">
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
                                <td colspan="4">
                                    Total Harga: Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    </div>

                    @if($transaksi->catatan)
                    <div style="padding: 0 1rem 1rem; margin-top: -1rem;">
                        <p style="margin: 0; font-size: 0.95rem;"><strong>Catatan Pesanan:</strong></p>
                        <p style="margin: 0; font-size: 0.95rem; white-space: normal; word-wrap: break-word;">{{ $transaksi->catatan }}</p>
                    </div>
                    @endif
                </div>
                <div class="order-footer">
                    
                    <!-- ▼▼▼ LOGIKA LABEL STATUS BARU ▼▼▼ -->
                    @if($transaksi->status == 'dibayar')
                        <!-- Kasus 1: Midtrans Sukses -->
                        <span class="badge-status badge-lunas">SUDAH LUNAS (Midtrans)</span>
                    
                    @elseif($transaksi->metode_pembayaran == 'CASH')
                        <!-- Kasus 2: Cash (Pasti Belum Bayar) -->
                        <span class="badge-status badge-belum-cash">BELUM BAYAR (Cash)</span>
                    
                    @else
                        <!-- Kasus 3: Midtrans tapi Belum Lunas -->
                        <!-- Menampilkan metode spesifik jika ada (misal 'qris' atau 'gopay' dari midtrans) -->
                        <span class="badge-status badge-belum-midtrans">
                            BELUM BAYAR (Transfer/QRIS)
                        </span>
                    @endif
                    <!-- ▲▲▲ AKHIR LOGIKA ▲▲▲ -->

                    <form action="{{ route('kasir.konfirmasi', $transaksi->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-konfirmasi">
                            Konfirmasi pesanan
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <p class="no-orders">Tidak ada pesanan pelanggan baru.</p>
        @endforelse
    </div>
@endsection

@push('scripts')
<script>
    // Refresh halaman setiap 30 detik
    setTimeout(function(){
       window.location.reload();
    }, 30000);
</script>
@endpush
