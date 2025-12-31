@extends('layouts.kasir')

@section('title', 'Riwayat Pemesanan')
@section('header_title', 'Riwayat Pemesanan')

@push('styles')
<style>
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
        gap: 1.5rem;
    }
    .order-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        overflow: hidden;
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
    .order-table td { border-bottom: 1px solid #eee; }
    .order-table .total-row { font-weight: bold; font-size: 1.1rem; }
    .order-table .total-row td { border-bottom: none; text-align: right; padding: 12px 8px; }
    
    /* Footer dengan Flexbox untuk tombol Hapus di kanan */
    .order-footer {
        padding: 0 1rem 1rem 1rem;
        display: flex;
        justify-content: space-between; /* Status kiri, Tombol kanan */
        align-items: center;
    }
    .status-text {
        font-weight: bold;
        font-size: 1rem;
        color: #16A34A; /* Hijau */
    }
    .no-orders {
        text-align: center;
        padding: 2rem;
        color: #777;
    }
    
    /* Layout Search Bar & Tombol Hapus Semua */
    .search-container {
        display: flex;
        justify-content: space-between; /* Jarak antara form cari dan tombol hapus */
        align-items: center;
        margin-bottom: 1.5rem;
        background-color: #f0f0f0;
        padding: 1rem;
        border-radius: 8px;
    }
    .search-form {
        display: flex;
        gap: 10px;
        flex: 1; /* Agar form cari mengambil sisa ruang */
    }
    .search-input {
        flex: 1;
        padding: 10px;
        border: 1px solid #BDBDBD;
        border-radius: 8px;
        font-size: 1rem;
    }
    .search-btn {
        padding: 10px 15px;
        border: none;
        border-radius: 8px;
        background-color: #B91C1C; /* Merah */
        color: white;
        font-size: 1rem;
        cursor: pointer;
    }
    
    /* Tombol Hapus Semua */
    .btn-hapus-semua {
        background-color: #B91C1C;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        text-decoration: none;
        margin-left: 1rem; /* Jarak dari search bar */
        white-space: nowrap;
    }
    .btn-hapus-semua:hover {
        background-color: #991B1B;
    }

    /* Tombol Hapus Item Kecil */
    .btn-hapus-item {
        background-color: #B91C1C;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 5px;
        font-size: 0.9rem;
        font-weight: bold;
        cursor: pointer;
    }
    .btn-hapus-item:hover {
        background-color: #991B1B;
    }
</style>
@endpush

@section('content')

    <!-- Pesan Sukses/Error -->
    @if (session('success'))
        <div style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div style="background-color: #FEE2E2; color: #B91C1C; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Container Search & Hapus Semua -->
    <div class="search-container">
        <form action="{{ route('kasir.riwayat') }}" method="GET" class="search-form">
            <input type="text" 
                   name="search" 
                   id="search-input" 
                   class="search-input" 
                   placeholder="Cari nama/meja/tanggal (dd/mm/yyyy)..."
                   value="{{ request('search') }}">
                   
            <button type="submit" id="search-btn" class="search-btn">Cari</button>
        </form>

        <!-- Tombol Hapus Seluruh Riwayat -->
        @if($transaksis_selesai->count() > 0)
        <form action="{{ route('kasir.riwayat.destroyAll') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus SELURUH riwayat pesanan? Data tidak bisa dikembalikan.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-hapus-semua">Hapus seluruh riwayat</button>
        </form>
        @endif
    </div>

    <div class="grid-container">
        @forelse ($transaksis_selesai as $transaksi)
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
                                <td colspan="4">
                                    Total Harga: Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="order-footer">
                    <span class="status-text">
                        Pesanan selesai
                    </span>

                    <!-- Tombol Hapus Item (Kanan) -->
                    <form action="{{ route('kasir.riwayat.destroy', $transaksi->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus riwayat pesanan ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-hapus-item">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="no-orders">
                @if (request('search'))
                    Tidak ada riwayat pesanan yang cocok dengan pencarian "{{ request('search') }}".
                @else
                    Belum ada riwayat pesanan yang selesai.
                @endif
            </p>
        @endforelse
    </div>
@endsection