<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $validatedData = $request->validate([
            'user_id'            => 'required|integer|exists:users,id',
            'nama_pemesan'       => 'required|string',
            'nomor_meja'         => 'required|integer',
            'total_harga'        => 'required|integer',
            'metode_pembayaran'  => 'required|string',
            'items'              => 'required|array|min:1',
            // [Pencegahan Cacat] item divalidasi: produk harus ada & jumlah minimal 1.
            'items.*.product_id' => 'required|integer|exists:produks,id',
            'items.*.jumlah'     => 'required|integer|min:1',
            'email'              => 'nullable|string',
            'catatan'            => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // [Efisiensi] Sebelumnya Produk::find() dipanggil di dalam foreach (1 query per item).
            // Sekarang semua produk diambil sekali dengan whereIn.
            $produkMap = Produk::whereIn('id', array_column($validatedData['items'], 'product_id'))
                ->get()
                ->keyBy('id');

            // [Komputasional] Total dihitung ulang di server (integer), bukan dari klien.
            $detailRows = [];
            $totalHarga = 0;

            foreach ($validatedData['items'] as $item) {
                $produk   = $produkMap->get($item['product_id']);
                $harga    = (int) $produk->price;
                $jumlah   = (int) $item['jumlah'];
                $subtotal = $harga * $jumlah;
                $totalHarga += $subtotal;

                $detailRows[] = [
                    'produk_id'    => $produk->id,
                    'nama_produk'  => $produk->name,
                    'harga_satuan' => $harga,
                    'jumlah'       => $jumlah,
                    'subtotal'     => $subtotal,
                ];
            }

            $transaksi = Transaksi::create([
                'user_id'           => $validatedData['user_id'],
                'nama_pemesan'      => $validatedData['nama_pemesan'],
                'nomor_meja'        => $validatedData['nomor_meja'],
                'total_harga'       => $totalHarga,
                'metode_pembayaran' => $validatedData['metode_pembayaran'],
                'status'            => 'menunggu_pembayaran',
                'catatan'           => $validatedData['catatan'] ?? null,
            ]);

            foreach ($detailRows as $row) {
                $row['transaksi_id'] = $transaksi->id;
                TransaksiDetail::create($row);
            }

            $snapToken = null;

            if ($validatedData['metode_pembayaran'] === 'MIDTRANS') {
                $snapToken = $this->buatSnapToken($transaksi, $validatedData);
            }

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Pesanan berhasil dibuat.',
                'transaksi_id' => $transaksi->id,
                'snap_token'   => $snapToken,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal buat pesanan: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Membatalkan pesanan dari Android.
     */
    public function cancelOrder($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        if ($transaksi->status !== 'menunggu_pembayaran') {
            return response()->json(['message' => 'Tidak bisa membatalkan pesanan ini'], 400);
        }

        $transaksi->details()->delete();
        $transaksi->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil dibatalkan',
        ]);
    }

    public function getTransaksiStatus($id)
    {
        $transaksi = Transaksi::with('details')->find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json([
            'success'   => true,
            'status'    => $transaksi->status,
            'transaksi' => $transaksi,
        ]);
    }

    /** Membuat Snap token Midtrans (kedaluwarsa 15 menit) dan menyimpannya. */
    private function buatSnapToken(Transaksi $transaksi, array $input): string
    {
        MidtransService::configure();

        $orderId = 'TRX-' . $transaksi->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $transaksi->total_harga,
            ],
            'customer_details' => [
                'first_name' => $input['nama_pemesan'],
                'email'      => $input['email'] ?? null,
            ],
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit'       => 'minutes',
                'duration'   => 15,
            ],
        ];

        $transaksi->snap_token        = Snap::getSnapToken($params);
        $transaksi->midtrans_order_id = $orderId;
        $transaksi->save();

        return $transaksi->snap_token;
    }
}
