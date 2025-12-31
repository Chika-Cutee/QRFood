<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>
    <!-- CSS Sederhana (mirip halaman login) -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D4C0A0; /* Background krem */
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 480px; /* Lebar HP */
            margin: 0 auto;
            background-color: #F0E6D8; /* Background konten */
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 1.5rem;
            box-sizing: border-box;
        }
        .header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .btn-back {
            background-color: #333;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .header h2 {
            font-size: 1.5rem;
            font-weight: bold;
            margin: 0;
        }
        
        /* Kotak Pesan Error */
        .error-box {
            background-color: #FEE2E2;
            color: #B91C1C;
            border: 1px solid #FCA5A5;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin: 1.5rem 0 0.5rem 0;
        }
        .divider {
            border-top: 3px dashed #aaa;
            margin: 1rem 0;
        }
        
        /* Daftar Pesanan */
        .item-card {
            display: flex;
            gap: 1rem;
            background: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 0.5rem;
        }
        .item-card img {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
        }
        .item-info {
            flex: 1;
        }
        .item-info h4 { margin: 0 0 5px 0; font-size: 1.1rem; }
        .item-info p { margin: 0; color: #B91C1C; font-weight: 600; }
        .item-total {
            text-align: right;
            font-size: 0.9rem;
        }
        
        /* Total, Nama, Meja */
        .summary-total {
            font-size: 1.25rem;
            font-weight: bold;
            margin: 1rem 0;
        }
        .summary-detail p {
            font-size: 1.1rem;
            margin: 0.5rem 0;
            font-weight: 600;
        }

        /* Metode Pembayaran */
        .payment-method {
            font-size: 1.1rem;
        }
        .payment-method label {
            display: block;
            margin-bottom: 0.8rem; /* Jarak antar opsi */
            cursor: pointer;
        }
        .payment-method input {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            vertical-align: middle; /* Agar rata tengah */
        }

        /* Tombol Aksi */
        .action-buttons {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 2rem;
        }
        .btn-action {
            flex: 1;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
        }
        .btn-kembali {
            background-color: #B91C1C;
            color: white;
        }
        .btn-lanjut {
            background-color: #B91C1C;
            color: white;
        }
        /* Style untuk tombol non-aktif */
        .btn-lanjut.disabled {
            background-color: #999;
            opacity: 0.7;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="javascript:history.back()" class="btn-back">&larr;</a>
            <h2>Detail Pesanan Anda</h2>
        </div>

        @if (session('error'))
            <div class="error-box">
                <strong>Oops!</strong> {{ session('error') }}
            </div>
        @endif
        
        <form action="{{ route('pesanan.buat') }}" method="POST">
            @csrf
            
            <input type="hidden" name="cart_data" value='{!! $cartJson !!}'>
            <input type="hidden" name="total_harga" value="{{ $totalHarga }}">
            <input type="hidden" name="nomor_meja" value="{{ $nomorMeja }}">
            
            <h3 class="section-title">Pesanan anda:</h3>
            <div class="divider"></div>

            @foreach ($items as $item)
            <div class="item-card">
                <img src="{{ asset($item->produk->image_url) }}" alt="{{ $item->produk->name }}">
                <div class="item-info">
                    <h4>{{ $item->produk->name }}</h4>
                    <p>Rp {{ number_format($item->produk->price, 0, ',', '.') }}</p>
                </div>
                <div class="item-total">
                    <p>Jumlah: {{ $item->jumlah }}</p>
                    <p>Total: Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach

            <div class="divider"></div>
            
            <p class="summary-total">Total: Rp {{ number_format($totalHarga, 0, ',', '.') }}</p>

            <div class="divider"></div>

            <div class="summary-detail">
                <p>Nama Pemesan: {{ $namaPemesan }}</p>
                <p>Nomor Meja : {{ $nomorMeja == 0 ? '??' : $nomorMeja }}</p>
            </div>

            <div class="divider"></div>

            <h3 class="section-title">Metode Pembayaran</h3>
            <div class="payment-method">
                <label>
                    <input type="radio" name="metode_pembayaran" value="CASH" class="payment-checkbox"> 
                    CASH (Bayar di Kasir)
                </label>
                <label>
                    <input type="radio" name="metode_pembayaran" value="MIDTRANS" class="payment-checkbox"> 
                    Transfer / QRIS (Midtrans)
                </label>
            </div>
            
            <div class="action-buttons">
                <button type="button" class="btn-action btn-kembali" onclick="history.back()">Kembali</button>
                <button type="submit" id="btn-lanjut-konfirmasi" class="btn-action btn-lanjut disabled" disabled>
                    Lanjut
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Ubah selector karena kita pakai radio button, tapi class masih sama
            const checkboxes = document.querySelectorAll('.payment-checkbox');
            const lanjutButton = document.getElementById('btn-lanjut-konfirmasi');

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    // Karena pakai radio button, kita tidak perlu mematikan yang lain secara manual
                    // Cukup cek apakah ada yang terpilih
                    
                    const isAnyChecked = Array.from(checkboxes).some(cb => cb.checked);

                    if (isAnyChecked) {
                        lanjutButton.classList.remove('disabled');
                        lanjutButton.disabled = false;
                    } else {
                        lanjutButton.classList.add('disabled');
                        lanjutButton.disabled = true;
                    }
                });
            });
        });
    </script>
</body>
</html>