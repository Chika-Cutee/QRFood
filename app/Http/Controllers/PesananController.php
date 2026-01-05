<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PesananController extends Controller
{
    // --- (Fungsi hitungPesanan & showKonfirmasi TETAP SAMA) ---
    public function hitungPesanan(Request $request)
    {
        $cartData = json_decode($request->input('cart'), true);
        if (empty($cartData)) return redirect()->route('user.dashboard')->with('error', 'Keranjang kosong.');

        $items = [];
        $totalHarga = 0;
        $produkIds = array_keys($cartData);
        $produks = Produk::whereIn('id', $produkIds)->get();

        foreach ($produks as $produk) {
            $jumlah = $cartData[$produk->id];
            if ($jumlah > 0) {
                $subtotal = $produk->price * $jumlah;
                $totalHarga += $subtotal;
                $items[] = (object) [
                    'produk' => [
                        'id' => $produk->id,
                        'name' => $produk->name,
                        'price' => $produk->price,
                        'image_url' => $produk->image_url,
                    ],
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotal,
                ];
            }
        }

        if (empty($items)) return redirect()->route('user.dashboard')->with('error', 'Keranjang kosong.');

        $nomorMeja = $request->session()->get('nomor_meja', 0);
        $namaPemesan = Auth::user()->name;

        $request->session()->put('konfirmasi_data', [
            'items' => $items,
            'totalHarga' => $totalHarga,
            'namaPemesan' => $namaPemesan,
            'nomorMeja' => $nomorMeja,
            'cartJson' => json_encode($items)
        ]);

        return redirect()->route('pesanan.konfirmasi.show');
    }

    public function showKonfirmasi(Request $request)
    {
        $data = $request->session()->get('konfirmasi_data');
        if (!$data) return redirect()->route('user.dashboard');
        $itemsAsObject = json_decode(json_encode($data['items']));
        
        return view('user.konfirmasi', [
            'items' => $itemsAsObject, 
            'totalHarga' => $data['totalHarga'],
            'namaPemesan' => $data['namaPemesan'],
            'nomorMeja' => $data['nomorMeja'],
            'cartJson' => $data['cartJson']
        ]);
    }

    /**
     * UPDATE: Auto Trigger Popup untuk Midtrans
     */
    public function buatPesanan(Request $request)
    {
        $request->validate([
            'total_harga' => 'required|integer',
            'nomor_meja' => 'required|integer', 
            'metode_pembayaran' => 'required|string|in:CASH,MIDTRANS',
            'cart_data' => 'required',
            'catatan' => 'nullable|string',
        ]);

        $cartItems = json_decode($request->cart_data, true);
        if (is_null($cartItems)) return redirect()->route('pesanan.konfirmasi.show');

        try {
            DB::beginTransaction();

            $transaksi = Transaksi::create([
                'user_id' => Auth::id(),
                'nama_pemesan' => Auth::user()->name,
                'nomor_meja' => $request->nomor_meja,
                'total_harga' => $request->total_harga,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status' => 'menunggu_pembayaran',
                'catatan' => $request->catatan,
            ]);

            foreach ($cartItems as $item) {
                TransaksiDetail::create([
                    'transaksi_id' => $transaksi->id,
                    'produk_id' => $item['produk']['id'],
                    'nama_produk' => $item['produk']['name'],
                    'harga_satuan' => $item['produk']['price'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $item['subtotal'],
                ]);
            }

            $snapToken = null;

            if ($request->metode_pembayaran == 'MIDTRANS') {
                Config::$serverKey = config('midtrans.server_key');
                Config::$isProduction = config('midtrans.is_production');
                Config::$isSanitized = config('midtrans.is_sanitized');
                Config::$is3ds = config('midtrans.is_3ds');

                $midtransOrderId = 'TRX-' . $transaksi->id . '-' . time();

                $params = [
                    'transaction_details' => [
                        'order_id' => $midtransOrderId,
                        'gross_amount' => $transaksi->total_harga,
                    ],
                    'customer_details' => [
                        'first_name' => Auth::user()->name,
                        'email' => Auth::user()->email,
                    ],
                ];

                $snapToken = Snap::getSnapToken($params);
                $transaksi->snap_token = $snapToken;
                $transaksi->midtrans_order_id = $midtransOrderId;
                $transaksi->save();
            }

            DB::commit();
            $request->session()->forget('konfirmasi_data');
            $request->session()->forget('nomor_meja');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pesanan.konfirmasi.show')->with('error', 'Gagal: ' . $e->getMessage());
        }

        if ($request->metode_pembayaran == 'CASH') {
            return redirect()->route('pesanan.sukses.cash', ['transaksi' => $transaksi->id]);
        } else {
            // Mengarahkan ke halaman bayar auto trigger
            return view('user.bayar_auto', compact('transaksi'));
        }
    }

    public function showSuksesCash(Transaksi $transaksi) {
        if ($transaksi->user_id !== Auth::id()) abort(403);
        // HARUS memuat relasi produk di dalam detail
        $transaksi->load('details.produk'); // ✅ DIPERBAIKI
        return view('user.sukses-cash', compact('transaksi'));
    }

    public function callback(Request $request) {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        try {
            $notif = new \Midtrans\Notification(); 
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        $parts = explode('-', $order_id); 
        $id_transaksi_asli = $parts[1]; 

        $transaksi = Transaksi::find($id_transaksi_asli);

        if (!$transaksi) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if ($transaction == 'capture') {
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    $transaksi->update(['status' => 'pending']);
                } else {
                    $transaksi->update(['status' => 'dibayar']);
                }
            }
        } else if ($transaction == 'settlement') {
            $transaksi->update(['status' => 'dibayar']);
        } else if ($transaction == 'pending') {
            $transaksi->update(['status' => 'menunggu_pembayaran']);
        } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
            $transaksi->update(['status' => 'batal']);
        }

        return response()->json(['message' => 'OK']);
    }

    // =========================================================
    //  TAMBAHAN FUNGSI BARU (Pending, Success, Cancel)
    // =========================================================

    /**
     * 1. Menampilkan Halaman Pending (Warna Orange)
     * Dipanggil jika user menutup popup Midtrans tanpa bayar.
     */
    public function pendingPage($id)
    {
        // Ambil data transaksi beserta detail itemnya
        $transaksi = Transaksi::with('details')->find($id);
        
        // Cek Keamanan:
        // Jika transaksi tidak ada, atau statusnya SUDAH DIBAYAR, jangan tampilkan halaman pending.
        // Langsung lempar ke halaman sukses atau menu.
        if (!$transaksi) {
            return redirect()->route('menu.index');
        }

        if ($transaksi->status == 'dibayar' || $transaksi->status == 'diproses') {
            return redirect()->route('order.success', $id);
        }

        // Jika statusnya bukan 'menunggu_pembayaran', kembali ke menu
        if ($transaksi->status != 'menunggu_pembayaran') {
            return redirect()->route('menu.index');
        }

        // Tampilkan View Pending
        // (Pastikan file resources/views/pending-midtrans.blade.php sudah ada)
        return view('user.pending-midtrans', compact('transaksi'));
    }

    /**
     * 2. Menampilkan Halaman Sukses Midtrans (Warna Hijau)
     */
    public function successPage($id)
    {
        $transaksi = Transaksi::with('details.produk')->find($id);

        if (!$transaksi) {
            return redirect()->route('user.dashboard')->with('error', 'Transaksi tidak ditemukan.');
        }

        // Logika tambahan: Cek ulang status ke Midtrans (Jaga-jaga jika callback telat)
        if ($transaksi->status == 'menunggu_pembayaran' && $transaksi->midtrans_order_id) {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');

            try {
                $status = Transaction::status($transaksi->midtrans_order_id);
                if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                    $transaksi->update(['status' => 'dibayar']); 
                }
                else if ($status->transaction_status == 'expire' || $status->transaction_status == 'cancel') {
                    $transaksi->update(['status' => 'batal']);
                }
            } catch (\Exception $e) {}
        }

        // Tampilkan View Sukses
        // (Sesuai nama file yang Anda upload: sukses-midtrans.blade.php)
        return view('user.sukses-midtrans', compact('transaksi'));
    }

    /**
     * 3. Fungsi Membatalkan Pesanan (Tombol Hapus)
     * Dipanggil saat user klik "Batalkan Pesanan" di halaman pending.
     */
    public function cancelOrder($id)
    {
        $transaksi = Transaksi::find($id);

        // Hanya boleh hapus jika statusnya masih 'menunggu_pembayaran'
        if ($transaksi && $transaksi->status == 'menunggu_pembayaran') {
            
            // PERBAIKAN 1: Gunakan 'details()', bukan 'transaksiDetails()'
            $transaksi->details()->delete(); 
            
            // Hapus transaksinya
            $transaksi->delete();

            // PERBAIKAN 2: Pastikan redirect ke 'user.dashboard', bukan 'menu.index'
            return redirect()->route('user.dashboard')->with('error', 'Pesanan dibatalkan.');
        }

        return redirect()->route('user.dashboard');
    }
    // FUNGSI INI YANG DICARI OLEH LARAVEL (Ganti/Tambahkan ini)
    public function showSuksesMidtrans($id)
    {
        // Cari transaksi
        $transaksi = Transaksi::with('details.produk')->find($id);

        // Jika tidak ada, kembalikan ke menu
        if (!$transaksi) {
            return redirect()->route('menu.index');
        }

        // Cek status lagi ke Midtrans untuk memastikan (Opsional tapi bagus)
        if ($transaksi->status == 'menunggu_pembayaran' && $transaksi->midtrans_order_id) {
            Config::$serverKey = config('midtrans.server_key');
            Config::$isProduction = config('midtrans.is_production');
            try {
                $status = Transaction::status($transaksi->midtrans_order_id);
                if ($status->transaction_status == 'settlement' || $status->transaction_status == 'capture') {
                    $transaksi->update(['status' => 'dibayar']);
                }
            } catch (\Exception $e) {}
        }

        // Tambahkan 'user.' di depannya
        return view('user.sukses-midtrans', compact('transaksi'));
    }
}