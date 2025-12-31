<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Pembayaran</title>
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>

    <style>
        body { font-family: Arial, sans-serif; background-color: #D4C0A0; margin: 0; padding: 0; }
        .container { width: 100%; max-width: 480px; margin: 0 auto; background-color: #F0E6D8; min-height: 100vh; box-shadow: 0 0 10px rgba(0,0,0,0.1); padding: 1.5rem; box-sizing: border-box; }
        .header { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; }
        .btn-back { background-color: #333; color: white; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 1.5rem; font-weight: bold; }
        .header h2 { font-size: 1.5rem; font-weight: bold; margin: 0; }
        .section-title { font-size: 1.2rem; font-weight: bold; margin: 1.5rem 0 0.5rem 0; }
        .divider { border-top: 3px dashed #aaa; margin: 1rem 0; }
        .item-card { display: flex; gap: 1rem; background: white; padding: 1rem; border-radius: 10px; margin-bottom: 0.5rem; align-items: center; }
        
        /* Tambahan CSS untuk Gambar */
        .item-image { 
            width: 70px; 
            height: 70px; 
            overflow: hidden; 
            border-radius: 8px; 
            flex-shrink: 0; 
        }
        .item-image img {
            width: 100%;
            height: 100%;
            object-fit: cover; 
        }

        .item-info { flex: 1; }
        .item-info h4 { margin: 0 0 5px 0; font-size: 1.1rem; }
        .item-info p { margin: 0; color: #B91C1C; font-weight: 600; }
        .item-total { text-align: right; font-size: 0.9rem; }
        .summary-total { font-size: 1.25rem; font-weight: bold; margin: 1rem 0; }
        .summary-detail p { font-size: 1.1rem; margin: 0.5rem 0; font-weight: 600; }
        
        /* Instruksi Pending (Warna Orange) */
        .pending-instructions {
            background-color: #fff;
            padding: 1rem;
            border-radius: 8px;
            border: 2px dashed #F59E0B; /* Orange */
            text-align: center;
        }
        .pending-instructions h3 { margin-top: 0; color: #D97706; }
        .pending-icon { font-size: 3rem; color: #F59E0B; display: block; margin-bottom: 10px; }

        /* Tombol Aksi */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 2rem;
        }

        .btn-pay {
            padding: 1rem; 
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            background-color: #1D4ED8; /* Biru */
            color: white;
            text-align: center;
            text-decoration: none;
            display: block; 
            width: 100%;
        }

        .btn-cancel {
            padding: 1rem; 
            border: 2px solid #B91C1C;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            background-color: transparent;
            color: #B91C1C;
            text-align: center;
            text-decoration: none;
            display: block;
            width: 100%;
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="{{ route('user.dashboard') }}" class="btn-back">&larr;</a>
            <h2>Menunggu Pembayaran</h2>
        </div>

        <div class="pending-instructions">
            <span class="pending-icon">⏳</span> 
            <h3>Belum Selesai!</h3>
            <p>Pembayaran Anda belum diselesaikan. Silakan lanjutkan pembayaran atau batalkan pesanan.</p>
            <h4 style="margin-bottom: 0;">ID Transaksi: #{{ $transaksi->id }}</h4>
        </div>

        <div class="divider"></div>

        <h3 class="section-title">Detail Pesanan:</h3>
        
        @foreach ($transaksi->details as $item)
        <div class="item-card">
            {{-- Logika Gambar --}}
            @if($item->produk && $item->produk->image_url)
            <div class="item-image">
                <img src="{{ asset($item->produk->image_url) }}" alt="{{ $item->nama_produk }}">
            </div>
            @else
            <div class="item-image" style="background-color: #eee;"></div>
            @endif

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
        
        <p class="summary-total">Total Tagihan: Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>

        <div class="divider"></div>

        <div class="action-buttons">
            <button id="pay-button" class="btn-pay">Lanjutkan Pembayaran</button>

            <a href="{{ route('order.cancel', $transaksi->id) }}" class="btn-cancel" onclick="return confirm('Yakin ingin membatalkan pesanan?');">Batalkan Pesanan</a>
        </div>
        
    </div>

    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            // Memanggil Snap Popup menggunakan Token yang sudah ada di database
            window.snap.pay('{{ $transaksi->snap_token }}', {
                onSuccess: function(result){
                    // Jika sukses, arahkan ke halaman sukses
                    window.location.href = "{{ route('order.success', $transaksi->id) }}";
                },
                onPending: function(result){
                    // Jika masih pending (misal tutup lagi), reload halaman ini saja
                    location.reload();
                },
                onError: function(result){
                    alert("Pembayaran gagal!");
                    location.reload();
                },
                onClose: function(){
                    alert('Anda menutup popup lagi belum selesai pembayaran');
                }
            });
        });
    </script>
</body>
</html>