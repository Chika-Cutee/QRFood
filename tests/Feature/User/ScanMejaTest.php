<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User; // <-- Jangan lupa import User

class ScanMejaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pengujian untuk memastikan scan QR meja menyimpan nomor ke sesi dan redirect.
     *
     * @return void
     */
    public function test_it_stores_table_number_in_session_and_redirects_to_dashboard(): void
    {
        // 1. Buat satu user dummy untuk login
        $user = User::factory()->create();

        // 2. Simulasikan request GET ke URL scan meja (misal: /meja/15)
        //    sambil login sebagai user yang baru dibuat.
        $response = $this->actingAs($user)->get('/meja/15');

        // 3. Lakukan Pengecekan (Assertion)
        //    - Pastikan sesi 'nomor_meja' berisi angka 15.
        $response->assertSessionHas('nomor_meja', 15);

        //    - Pastikan responsnya adalah redirect ke route 'user.dashboard'.
        $response->assertRedirect(route('user.dashboard'));
    }

    
}
