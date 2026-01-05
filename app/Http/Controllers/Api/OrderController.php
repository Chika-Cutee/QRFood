<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function createOrder(Request $request)
{
    // 1. Validasi
    $validatedData = $request->validate([
        'user_id' => 'required|integer|exists:users,id',
        'nama_pemesan' => 'required|string',
        'nomor_meja' => 'required|integer',
        'total_harga' => 'required|integer',
        'metode_pembayaran' => 'required|string',
        'items' => 'required|array',
        'email' => 'nullable|string',
        'catatan' => 'nullable|string', // Validasi input catatan
    ]);

    try {
        DB::beginTransaction();

        // 2. Simpan Transaksi ke Database (TABEL UTAMA)
        $transaksi = Transaksi::create([
            'user_id' => $validatedData['user_id'],
            'nama_pemesan' => $validatedData['nama_pemesan'],
            'nomor_meja' => $validatedData['nomor_meja'],
            'total_harga' => $validatedData['total_harga'],
            'metode_pembayaran' => $validatedData['metode_pembayaran'],
            'status' => 'menunggu_pembayaran',
            'catatan' => $request->catatan, // <--- BENAR: Simpan di sini (Tabel Transaksi)
        ]);

        // 3. Simpan Detail Item
        foreach ($validatedData['items'] as $item) {
            $produk = Produk::find($item['product_id']);
            TransaksiDetail::create([
                'transaksi_id' => $transaksi->id,
                'produk_id' => $item['product_id'],
                'nama_produk' => $produk->name,
                'harga_satuan' => $produk->price,
                'jumlah' => $item['jumlah'],
                'subtotal' => $item['jumlah'] * $produk->price,
                // 'catatan' => $request->catatan,  <--- SALAH: Jangan simpan di detail, nanti berulang-ulang
            ]);
        }

            $snapToken = null;

            if ($validatedData['metode_pembayaran'] == 'MIDTRANS') {
                Config::$serverKey = config('midtrans.server_key');
                Config::$isProduction = config('midtrans.is_production');
                Config::$isSanitized = config('midtrans.is_sanitized');
                Config::$is3ds = config('midtrans.is_3ds');

                $customOrderId = 'TRX-' . $transaksi->id . '-' . time();

                $params = [
                    'transaction_details' => [
                        'order_id' => $customOrderId,
                        'gross_amount' => $transaksi->total_harga,
                    ],
                    'customer_details' => [
                        'first_name' => $validatedData['nama_pemesan'],
                        'email'      => $request->email,
                    ],
                    'expiry' => [
                        'start_time' => date("Y-m-d H:i:s O"),
                        'unit' => 'minutes',
                        'duration' => 15
                    ],
                ];

                $snapToken = Snap::getSnapToken($params);
                $transaksi->snap_token = $snapToken;
                $transaksi->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat.',
                'transaksi_id' => $transaksi->id,
                'snap_token' => $snapToken 
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal buat pesanan: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage()
            ], 500);
        }
    }
    // Fungsi untuk membatalkan pesanan dari Android
    public function cancelOrder($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        if ($transaksi->status == 'menunggu_pembayaran') {
            // Hapus detail dan transaksi
            $transaksi->details()->delete();
            $transaksi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan'
            ]);
        }

        return response()->json(['message' => 'Tidak bisa membatalkan pesanan ini'], 400);
    }

    public function getTransaksiStatus($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }
        
        // Pastikan TransaksiDetail juga dimuat jika perlu
        $transaksi->load('details');

        return response()->json([
            'success' => true,
            'status' => $transaksi->status,
            'transaksi' => $transaksi // Kirim objek transaksi lengkap
        ]);
    }
}