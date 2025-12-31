<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #D4C0A0; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 480px; margin: 0 auto; background-color: #F0E6D8; min-height: 100vh; box-shadow: 0 0 10px rgba(0,0,0,0.1); padding: 1.5rem; box-sizing: border-box; }
        .header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
        .btn-back { background-color: #333; color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 1.5rem; font-weight: bold; }
        .header h2 { font-size: 1.5rem; font-weight: bold; margin: 0; }
        .section-title { font-size: 1.2rem; font-weight: bold; margin: 1.5rem 0 0.5rem 0; }
        .divider { border-top: 3px dashed #aaa; margin: 1rem 0; }
        
        /* === MODIFIKASI CSS UNTUK ITEM CARD === */
        .item-card { 
            display: flex; 
            gap: 1rem; 
            background: white; 
            padding: 1rem; 
            border-radius: 10px; 
            margin-bottom: 0.5rem; 
            align-items: center; /* Menyelaraskan item secara vertikal */
        }
        .item-image { 
            width: 70px; /* Lebar gambar */
            height: 70px; /* Tinggi gambar */
            overflow: hidden; 
            border-radius: 8px; 
            flex-shrink: 0; /* Mencegah gambar menyusut */
        }
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Memastikan gambar memenuhi area tanpa merusak aspek rasio */
        }
        .item-info { flex: 1; }
        .item-info h4 { margin: 0 0 5px 0; font-size: 1.1rem; }
        .item-info p { margin: 0; color: #B91C1C; font-weight: 600; }
        .item-total { text-align: right; font-size: 0.9rem; }
        /* === AKHIR MODIFIKASI CSS === */

        .summary-total { font-size: 1.25rem; font-weight: bold; margin: 1rem 0; }
        .summary-detail p { font-size: 1.1rem; margin: 0.5rem 0; font-weight: 600; }
        
        /* Instruksi Pembayaran */
        .payment-instructions {
            background-color: #fff;
            padding: 1rem;
            border-radius: 8px;
            border: 2px dashed #16A34A;
            text-align: center;
        }
        .payment-instructions h3 { margin-top: 0; color: #16A34A; }
        .success-icon { font-size: 3rem; color: #16A34A; display: block; margin-bottom: 10px; }

        /* Wrapper untuk menengahkan tombol */
        .selesai-container {
            text-align: center;
            margin-top: 2rem;
        }

        .btn-selesai {
            padding: 1rem 2rem; 
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            background-color: #B91C1C;
            color: white;
            text-align: center;
            text-decoration: none;
            display: inline-block; 
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="{{ route('user.dashboard') }}" class="btn-back">&larr;</a>
            <h2>Pembayaran Berhasil</h2>
        </div>

        <div class="payment-instructions">
            <span class="success-icon">✔</span>
            <h3>Pembayaran Berhasil!</h3>
            <p>Terima kasih, pembayaran Anda telah kami terima. Pesanan Anda sedang diproses.</p>
            <h4 style="margin-bottom: 0;">ID Transaksi: #{{ $transaksi->id }}</h4>
        </div>

        <div class="divider"></div>

        <h3 class="section-title">Detail Pesanan:</h3>
        
        @foreach ($transaksi->details as $item)
        <div class="item-card">
            
            {{-- === MODIFIKASI HTML UNTUK GAMBAR === --}}
            @if($item->produk && $item->produk->image_url)
            <div class="item-image">
                <img src="{{ asset($item->produk->image_url) }}" alt="{{ $item->nama_produk }}">
            </div>
            @else
            <div class="item-image" style="background-color: #eee;">
                {{-- Placeholder jika gambar tidak ada --}}
            </div>
            @endif
            {{-- === AKHIR MODIFIKASI HTML === --}}

            <div class="item-info">
                <h4>{{ $item->nama_produk }}</h4>
                <p>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</p>
            </div>
            <div class="item-total">
                <p>Jumlah: {{ $item->jumlah }}</p>
                <p>Total: Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
            </div>
        </div>
        @endforeach

        <div class="divider"></div>
        
        <p class="summary-total">Total Dibayar: Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>

        <div class="divider"></div>

        <div class="summary-detail">
            <p>Nama Pemesan: {{ $transaksi->nama_pemesan }}</p>
            <p>Nomor Meja : {{ $transaksi->nomor_meja == 0 ? '??' : $transaksi->nomor_meja }}</p>
            <p>Metode: <strong>Transfer / QRIS</strong></p>
        </div>

        <div class="selesai-container">
            <a href="{{ route('user.dashboard') }}" id="btn-selesai" class="btn-selesai">Selesai</a>
        </div>
        
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Hapus keranjang dari ingatan browser karena transaksi sukses
            localStorage.removeItem('gerai_cart');
        });
    </script>
</body>
</html>