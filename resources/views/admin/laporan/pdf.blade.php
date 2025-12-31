<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <!-- Ini penting agar dompdf bisa membaca karakter -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan - {{ $kasir->name }} - {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</title>
    <!-- CSS Wajib ditaruh di dalam tag <style> (inline) agar terbaca oleh PDF -->
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            font-size: 12px;
        }
        .container {
            width: 100%;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            font-size: 24px;
            margin: 0;
        }
        .header p {
            font-size: 14px;
            margin: 5px 0;
        }
        .laporan-info {
             margin-bottom: 20px;
        }
        .laporan-info p {
            font-size: 14px;
            margin: 5px 0;
        }
        .laporan-table {
            width: 100%;
            border-collapse: collapse; /* Penting */
            margin-bottom: 20px;
        }
        .laporan-table th, .laporan-table td {
            padding: 8px;
            border: 1px solid #333; /* Border hitam */
            text-align: center;
        }
        .laporan-table th {
            background-color: #B91C1C;
            color: white;
            font-size: 14px;
        }
        /* Kolom Nama Menu rata kiri */
        .laporan-table td:nth-child(2) {
            text-align: left;
        }
        .total-pendapatan {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            margin-top: 10px;
        }
        .total-pendapatan span {
            color: #B91C1C;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="header">
            <h1>Laporan Penjualan Kasir</h1>
            <p>Cafe Gerai 3 Abdul</p>
        </div>

        <!-- Info Laporan -->
        <div class="laporan-info">
            <p><strong>Nama kasir:</strong> {{ $kasir->name }}</p>
            <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($tanggal)->format('d F Y') }}</p>
        </div>

        <!-- Tabel Item -->
        <table class="laporan-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Menu</th>
                    <th>Jumlah</th>
                    <th>Harga Satuan</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($daftarPenjualan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->nama_produk }}</td>
                    <td>{{ $item->total_jumlah }}</td>
                    <td>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($item->total_subtotal, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">Tidak ada penjualan untuk laporan ini.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Total Pendapatan -->
        <div class="total-pendapatan">
            Total Pendapatan Hari Ini: 
            <span>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
        </div>

    </div>
</body>
</html>