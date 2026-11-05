<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Transaction;

class PesananController extends Controller
{
    private const STATUS_MENUNGGU = 'menunggu_pembayaran';
    private const STATUS_DIBAYAR  = 'dibayar';
    private const STATUS_DIPROSES = 'diproses';
    private const STATUS_BATAL    = 'batal';
    private const STATUS_PENDING  = 'pending';

    public function hitungPesanan(Request $request)
    {
        $cartData = json_decode((string) $request->input('cart'), true);

        // [Pencegahan Cacat] json_decode bisa menghasilkan null/skalar bila input rusak.
        if (!is_array($cartData) || empty($cartData)) {
            return redirect()->route('user.dashboard')->with('error', 'Keranjang kosong.');
        }

        $produks = Produk::whereIn('id', array_keys($cartData))->get();

        $items      = [];
        $totalHarga = 0;

        foreach ($produks as $produk) {
            // [Pencegahan Cacat] jumlah dipaksa integer agar perhitungan harga tidak
            // terkena string/float dari input pengguna.
            $jumlah = (int) ($cartData[$produk->id] ?? 0);

            if ($jumlah > 0) {
                // [Komputasional] satu pernyataan sederhana + integer (tanpa floating point).
                $harga    = (int) $produk->price;
                $subtotal = $harga * $jumlah;
                $totalHarga += $subtotal;

                $items[] = (object) [
                    'produk' => [
                        'id'        => $produk->id,
                        'name'      => $produk->name,
                        'price'     => $harga,
                        'image_url' => $produk->image_url,
                    ],
                    'jumlah'   => $jumlah,
                    'subtotal' => $subtotal,
                ];
            }
        }

        if (empty($items)) {
            return redirect()->route('user.dashboard')->with('error', 'Keranjang kosong.');
        }

        $request->session()->put('konfirmasi_data', [
            'items'       => $items,
            'totalHarga'  => $totalHarga,
            'namaPemesan' => Auth::user()->name,
            'nomorMeja'   => $request->session()->get('nomor_meja', 0),
            'cartJson'    => json_encode($items),
        ]);

        return redirect()->route('pesanan.konfirmasi.show');
    }

    public function showKonfirmasi(Request $request)
    {
        $data = $request->session()->get('konfirmasi_data');

        if (!$data) {
            return redirect()->route('user.dashboard');
        }

        return view('user.konfirmasi', [
            'items'       => json_decode(json_encode($data['items'])),
            'totalHarga'  => $data['totalHarga'],
            'namaPemesan' => $data['namaPemesan'],
            'nomorMeja'   => $data['nomorMeja'],
            'cartJson'    => $data['cartJson'],
        ]);
    }

    public function buatPesanan(Request $request)
    {
        $request->validate([
            'total_harga'       => 'required|integer',
            'nomor_meja'        => 'required|integer',
            'metode_pembayaran' => 'required|string|in:CASH,MIDTRANS',
            'cart_data'         => 'required',
            'catatan'           => 'nullable|string',
        ]);

        $cartItems = json_decode($request->cart_data, true);

        if (!is_array($cartItems) || empty($cartItems)) {
            return redirect()->route('pesanan.konfirmasi.show');
        }

        try {
            DB::beginTransaction();

            // [Komputasional] Harga & total dihitung ulang dari database (integer),
            // bukan dipercaya dari form yang bisa dimanipulasi pengguna.
            $produkMap = Produk::whereIn('id', array_column(array_column($cartItems, 'produk'), 'id'))
                ->get()
                ->keyBy('id');

            $detailRows = [];
            $totalHarga = 0;

            foreach ($cartItems as $item) {
                $produk = $produkMap->get($item['produk']['id'] ?? null);
                $jumlah = (int) ($item['jumlah'] ?? 0);

                if ($produk === null || $jumlah < 1) {
                    throw new \InvalidArgumentException('Item pesanan tidak valid.');
                }

                $harga    = (int) $produk->price;
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
                'user_id'           => Auth::id(),
                'nama_pemesan'      => Auth::user()->name,
                'nomor_meja'        => $request->nomor_meja,
                'total_harga'       => $totalHarga,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status'            => self::STATUS_MENUNGGU,
                'catatan'           => $request->catatan,
            ]);

            foreach ($detailRows as $row) {
                $row['transaksi_id'] = $transaksi->id;
                TransaksiDetail::create($row);
            }

            if ($request->metode_pembayaran === 'MIDTRANS') {
                $this->buatSnapToken($transaksi);
            }

            DB::commit();
            $request->session()->forget(['konfirmasi_data', 'nomor_meja']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat pesanan: ' . $e->getMessage());

            return redirect()->route('pesanan.konfirmasi.show')->with('error', 'Gagal: ' . $e->getMessage());
        }

        if ($request->metode_pembayaran === 'CASH') {
            return redirect()->route('pesanan.sukses.cash', ['transaksi' => $transaksi->id]);
        }

        return view('user.bayar_auto', compact('transaksi'));
    }

    public function showSuksesCash(Transaksi $transaksi)
    {
        if ($transaksi->user_id !== Auth::id()) {
            abort(403);
        }

        $transaksi->load('details.produk');

        return view('user.sukses-cash', compact('transaksi'));
    }

    /**
     * Notifikasi (webhook) Midtrans.
     * [Pedoman Kontrol] satu titik keluar ($response), rantai if-else diganti switch + default.
     */
    public function callback(Request $request)
    {
        MidtransService::configure();

        try {
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        // [Pencegahan Cacat] Sebelumnya $parts[1] diakses tanpa dicek -> error
        // "Undefined array key" bila format order_id tidak sesuai "TRX-{id}-{waktu}".
        $idTransaksi = $this->ambilIdTransaksi((string) $notif->order_id);
        $transaksi   = $idTransaksi !== null ? Transaksi::find($idTransaksi) : null;

        if ($transaksi === null) {
            $response = response()->json(['message' => 'Order not found'], 404);
        } else {
            $statusBaru = $this->tentukanStatus(
                (string) $notif->transaction_status,
                (string) $notif->payment_type,
                (string) $notif->fraud_status
            );

            if ($statusBaru !== null) {
                $transaksi->update(['status' => $statusBaru]);
            }

            $response = response()->json(['message' => 'OK']);
        }

        return $response;
    }

    /**
     * Halaman pending (user menutup popup Midtrans tanpa membayar).
     */
    public function pendingPage($id)
    {
        $transaksi = Transaksi::with('details')->find($id);

        // [Pencegahan Cacat] Rute 'menu.index' tidak ada di routes/web.php sehingga
        // redirect lama melempar RouteNotFoundException. Diganti 'user.dashboard'.
        if (!$transaksi) {
            return redirect()->route('user.dashboard');
        }

        switch ($transaksi->status) {
            case self::STATUS_MENUNGGU:
                return view('user.pending-midtrans', compact('transaksi'));

            case self::STATUS_DIBAYAR:
            case self::STATUS_DIPROSES:
                return redirect()->route('order.success', $id);

            default:
                return redirect()->route('user.dashboard');
        }
    }

    /**
     * Halaman sukses Midtrans. Dipakai juga oleh showSuksesMidtrans()
     * (sebelumnya dua method hampir identik -> duplikasi rutinitas).
     */
    public function successPage($id)
    {
        $transaksi = Transaksi::with('details.produk')->find($id);

        if (!$transaksi) {
            return redirect()->route('user.dashboard')->with('error', 'Transaksi tidak ditemukan.');
        }

        $this->sinkronkanStatusDariMidtrans($transaksi);

        return view('user.sukses-midtrans', compact('transaksi'));
    }

    public function showSuksesMidtrans($id)
    {
        return $this->successPage($id);
    }

    /**
     * Pembatalan hanya boleh jika status masih 'menunggu_pembayaran'.
     */
    public function cancelOrder($id)
    {
        $transaksi = Transaksi::find($id);

        if ($transaksi && $transaksi->status === self::STATUS_MENUNGGU) {
            // Detail ikut terhapus oleh onDelete('cascade') di migrasi; delete eksplisit dipertahankan.
            $transaksi->details()->delete();
            $transaksi->delete();

            return redirect()->route('user.dashboard')->with('error', 'Pesanan dibatalkan.');
        }

        return redirect()->route('user.dashboard');
    }

    // ------------------------------------------------------------------
    //  Rutinitas bantu (private)
    // ------------------------------------------------------------------

    /** Membuat Snap token + order id Midtrans lalu menyimpannya ke transaksi. */
    private function buatSnapToken(Transaksi $transaksi): void
    {
        MidtransService::configure();

        $midtransOrderId = 'TRX-' . $transaksi->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id'     => $midtransOrderId,
                'gross_amount' => (int) $transaksi->total_harga,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email'      => Auth::user()->email,
            ],
        ];

        $transaksi->snap_token        = Snap::getSnapToken($params);
        $transaksi->midtrans_order_id = $midtransOrderId;
        $transaksi->save();
    }

    /** Mengambil id transaksi dari order id berformat "TRX-{id}-{timestamp}"; null jika tidak valid. */
    private function ambilIdTransaksi(string $orderId): ?int
    {
        if (preg_match('/^TRX-(\d+)-\d+$/', $orderId, $cocok) === 1) {
            return (int) $cocok[1];
        }

        return null;
    }

    /**
     * Memetakan status Midtrans -> status internal. null = tidak ada perubahan.
     * [Pedoman Kontrol] switch dengan 'default'.
     */
    private function tentukanStatus(string $transaction, string $type, string $fraud): ?string
    {
        switch ($transaction) {
            case 'capture':
                if ($type !== 'credit_card') {
                    return null;
                }
                return $fraud === 'challenge' ? self::STATUS_PENDING : self::STATUS_DIBAYAR;

            case 'settlement':
                return self::STATUS_DIBAYAR;

            case 'pending':
                return self::STATUS_MENUNGGU;

            case 'deny':
            case 'expire':
            case 'cancel':
                return self::STATUS_BATAL;

            default:
                return null;
        }
    }

    /** Cek ulang status ke Midtrans (jaga-jaga bila callback terlambat). */
    private function sinkronkanStatusDariMidtrans(Transaksi $transaksi): void
    {
        if ($transaksi->status !== self::STATUS_MENUNGGU || !$transaksi->midtrans_order_id) {
            return;
        }

        MidtransService::configure();

        try {
            $status     = Transaction::status($transaksi->midtrans_order_id);
            $statusBaru = $this->tentukanStatus((string) $status->transaction_status, 'credit_card', '');

            if ($statusBaru === self::STATUS_DIBAYAR || $statusBaru === self::STATUS_BATAL) {
                $transaksi->update(['status' => $statusBaru]);
            }
        } catch (\Exception $e) {
            // [Pencegahan Cacat] Sebelumnya catch kosong (error hilang tanpa jejak).
            Log::warning('Gagal cek status Midtrans: ' . $e->getMessage());
        }
    }
}
