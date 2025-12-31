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
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        overflow: hidden;
        background-color: #fff;
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
    .order-body { padding: 1rem; }
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
    /* Atur lebar kolom: Menu lebih lebar */
    .order-table th:nth-child(1), .order-table td:nth-child(1) { width: 70%; text-align: left; } 
    .order-table th:nth-child(2), .order-table td:nth-child(2) { width: 30%; } 

    .order-footer {
        padding: 0 1rem 1rem 1rem;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .btn-selesai {
        background-color: #16A34A;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-weight: bold;
        cursor: pointer;
        width: 100%;
        font-size: 1rem;
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
                        <p><strong>Meja {{ $transaksi->nomor_meja }}</strong></p>
                        <p>#{{ $transaksi->id }}</p>
                        <div>
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
                    </div>

                    <div class="order-footer">
                        <form action="{{ route('koki.selesai', $transaksi->id) }}" method="POST" onsubmit="return confirm('Pesanan sudah selesai dimasak?')" style="width: 100%;">
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
