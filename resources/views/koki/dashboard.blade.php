@extends('layouts.koki')

@section('header_title', 'Daftar Pesanan (Perlu Dimasak)')

@push('styles')
<style>
    .grid-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }
    .order-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        overflow: hidden;
        background-color: #fff;
        /* --- KUNCI: Menjaga tinggi kartu seragam dan tombol di bawah --- */
        display: flex;
        flex-direction: column;
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
        /* --- KUNCI: Body mengambil sisa ruang agar footer terdorong ke bawah --- */
        flex-grow: 1; 
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
    .order-table th:nth-child(1), .order-table td:nth-child(1) { width: 70%; text-align: left; } 
    .order-table th:nth-child(2), .order-table td:nth-child(2) { width: 30%; } 

    /* --- FOOTER: Sekarang berada di dalam order-card --- */
    .order-footer {
        padding: 1rem;
        border-top: 1px solid #eee;
        background-color: #fcfcfc;
        margin-top: auto; /* Memastikan nempel di bawah */
    }
    .btn-selesai {
        background-color: #16A34A;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        font-size: 1rem;
        transition: 0.3s;
    }
    .btn-selesai:hover { background-color: #15803D; }
    .no-orders { text-align: center; padding: 2rem; color: #777; }
</style>
@endpush

@section('content')
    @if($transaksis->isEmpty())
        <p class="no-orders">Tidak ada pesanan yang perlu dimasak saat ini.</p>
    @else
        <div class="grid-container">
            @foreach($transaksis as $transaksi)
                <div class="order-card">
                    <div class="order-header">
                        <p><strong>{{ $transaksi->nama_pemesan }}</strong></p>
                        <p><strong>Meja {{ $transaksi->nomor_meja == 0 ? '??' : $transaksi->nomor_meja }}</strong></p>
                        <p>#{{ $transaksi->id }}</p>
                        <div style="text-align: right;">
                            <p>{{ $transaksi->updated_at->format('H:i') }} WIB</p>
                            <small style="color: #d32f2f; font-weight: bold;">{{ $transaksi->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    
                    <div class="order-body">
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>Menu</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transaksi->details as $detail)
                                    <tr>
                                        <td>{{ $detail->nama_produk }}</td>
                                        <td>{{ $detail->jumlah }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        @if($transaksi->catatan)
                        <div style="margin-top: 10px;">
                            <p style="margin: 0 0 5px 0; font-size: 0.95rem;"><strong>Catatan Pesanan:</strong></p>
                            <p style="margin: 0; font-size: 0.95rem; background-color: #FEF3C7; padding: 10px; border-radius: 4px; border-left: 4px solid #F59E0B; white-space: normal; word-wrap: break-word;">
                                {{ $transaksi->catatan }}
                            </p>
                        </div>
                        @endif
                    </div>

                    <div class="order-footer">
                        <form action="{{ route('koki.selesai', $transaksi->id) }}" method="POST" onsubmit="return confirm('Pesanan sudah selesai dimasak?')" style="width: 100%; margin: 0;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn-selesai">
                                Selesai Masak / Siap Saji
                            </button>
                        </form>
                    </div>
                </div> 
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
<script>
    // Refresh halaman setiap 30 detik untuk mengecek pesanan baru
    setTimeout(function(){
       window.location.reload();
    }, 30000);
</script>
@endpush