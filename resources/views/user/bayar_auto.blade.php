<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Memproses Pembayaran...</title>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
    <style>
        /* Loader sederhana agar user tahu sedang proses */
        body { 
            font-family: Arial, sans-serif; 
            background-color: #D4C0A0; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
            flex-direction: column;
        }
        .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #B91C1C;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        p { color: #333; font-weight: bold; }
        
        /* Tombol cadangan jika popup tidak muncul otomatis (misal diblokir browser) */
        .btn-manual {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #B91C1C;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>
<body>
    
    <div class="loader"></div>
    <p>Mohon tunggu, membuka pembayaran...</p>

    <!--<button id="pay-button" class="btn-manual">Klik disini jika popup tidak muncul</button> -->

    <script type="text/javascript">
      // Fungsi untuk memunculkan Snap
      function triggerSnap() {
        window.snap.pay('{{ $transaksi->snap_token }}', {
          
          // 1. Jika SUKSES -> Ke Halaman Sukses (Hijau)
          onSuccess: function(result){
            window.location.href = "{{ route('pesanan.sukses.midtrans', $transaksi->id) }}";
          },
          
          // 2. Jika PENDING -> Ke Halaman Pending (Orange)
          // (Jangan ke halaman sukses!)
          onPending: function(result){
             window.location.href = "{{ route('order.pending', $transaksi->id) }}";
          },
          
          // 3. Jika ERROR -> Ke Halaman Pending (Orange)
          onError: function(result){
            alert("Pembayaran gagal!");
            window.location.href = "{{ route('order.pending', $transaksi->id) }}";
          },
          
          // 4. Jika DITUTUP (X) -> Ke Halaman Pending (Orange)
          // (Ini perbaikan utamanya)
          onClose: function(){
             window.location.href = "{{ route('order.pending', $transaksi->id) }}";
          }
        });
      }

      // Otomatis klik saat halaman dimuat
      document.addEventListener("DOMContentLoaded", function() {
          triggerSnap();
      });

      // Event listener tombol manual
      document.getElementById('pay-button').addEventListener('click', function(){
          triggerSnap();
      });
    </script>
</body>
</html>