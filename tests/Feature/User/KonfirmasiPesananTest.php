<?php

namespace Tests\Feature\Kasir;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Kasir;
use App\Models\User;
use App\Models\Transaksi;

class KonfirmasiPesananTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Memastikan kasir bisa mengkonfirmasi pesanan dan statusnya berubah.
     */
    public function test_kasir_can_confirm_order(): void
    {
        // 1. Buat data kasir dan user dummy
        $kasir = Kasir::factory()->create();
        $user = User::factory()->create();

        // 2. Buat satu transaksi yang statusnya masih menunggu_pembayaran
        $transaksi = Transaksi::create([
            'user_id' => $user->id,
            'nama_pemesan' => $user->name,
            'nomor_meja' => 5,
            'total_harga' => 50000,
            'metode_pembayaran' => 'CASH',
            'status' => 'menunggu_pembayaran'
        ]);

        // 3. Simulasikan Kasir melakukan request POST ke route konfirmasi
        // Perhatikan kita menggunakan guard 'kasir' di fungsi actingAs()
        $response = $this->actingAs($kasir, 'kasir')->post(route('kasir.konfirmasi', $transaksi->id));

        // 4. Cek apakah kasir di-redirect kembali ke dashboard
        $response->assertRedirect(route('kasir.dashboard'));
        $response->assertSessionHas('success'); // Pesan sukses muncul

        // 5. Cek DATABASE: Pastikan statusnya benar-benar berubah jadi 'diproses'
        $this->assertDatabaseHas('transaksis', [
            'id' => $transaksi->id,
            'status' => 'diproses',
            'kasir_id' => $kasir->id // Pastikan ID kasir yang memproses tersimpan
        ]);
    }
}
