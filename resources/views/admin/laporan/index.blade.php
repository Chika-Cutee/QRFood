@extends('layouts.admin')

@section('title', 'Laporan Penjualan Harian')
@section('header_title', 'Laporan Penjualan Harian')

@push('styles')
<style>
    /* Style ini mirip dengan 'Kelola Akun Kasir' */
    .laporan-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    .laporan-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem 1.5rem;
        background-color: white;
        border: 2px solid #ddd;
        border-radius: 1rem;
        text-decoration: none;
        color: #333;
        font-weight: 600;
        font-size: 1.1rem;
        transition: border-color 0.2s, background-color 0.2s;
    }
    .laporan-item:hover {
        border-color: #B91C1C;
        background-color: #fcfcfc;
    }
    .no-laporan {
        text-align: center;
        padding: 2rem;
        color: #777;
    }
</style>
@endpush

@section('content')

    <!-- Tampilkan Pesan Sukses (jika ada) -->
    @if (session('success'))
        <div style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="laporan-list">
        @forelse ($laporan_grup as $laporan)
            <!-- Setiap item adalah link ke halaman detail -->
            <!-- Kirim kasir_id DAN tanggal (string 'Y-m-d') -->
            <a href="{{ route('admin.laporan.show', ['kasir_id' => $laporan->kasir_id, 'tanggal' => $laporan->tanggal]) }}" class="laporan-item">
                
                <span>
                    <!-- Tampilkan nama kasir (jika ada), atau "Kasir Terhapus" -->
                    {{ $laporan->kasir->name ?? '[Kasir Terhapus]' }}
                </span>
                
                <span>
                    <!-- Tampilkan hari dan tanggal format Indonesia -->
                    {{ \Carbon\Carbon::parse($laporan->tanggal)->locale('id')->translatedFormat('l, d-m-Y') }}
                </span>
            </a>
        @empty
            <p class="no-laporan">Belum ada laporan penjualan yang selesai.</p>
        @endforelse
    </div>

@endsection