<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\PesananController;

class PesananControllerTest extends TestCase
{
    public function test_TC01_hitung_total_normal()
    {
        $controller = new PesananController();
        $items = [
            ['harga' => 15000, 'jumlah' => 2], // 30.000
            ['harga' => 10000, 'jumlah' => 1]  // 10.000
        ];
        $hasil = $controller->kalkulasiTotalBelanja($items);
        $this->assertEquals(40000, $hasil);
    }

    public function test_TC02_keranjang_kosong()
    {
        $controller = new PesananController();
        $hasil = $controller->kalkulasiTotalBelanja([]);
        $this->assertEquals("Error: Keranjang Kosong", $hasil);
    }

    public function test_TC03_data_tidak_lengkap()
    {
        $controller = new PesananController();
        $items = [
            ['harga' => 15000] // Key 'jumlah' sengaja dihilangkan
        ];
        $hasil = $controller->kalkulasiTotalBelanja($items);
        $this->assertEquals("Error: Data tidak lengkap", $hasil);
    }

    public function test_TC04_input_minus()
    {
        $controller = new PesananController();
        $items = [
            ['harga' => 15000, 'jumlah' => -1]
        ];
        $hasil = $controller->kalkulasiTotalBelanja($items);
        $this->assertEquals("Error: Nilai tidak boleh minus", $hasil);
    }

    public function test_TC05_input_string()
    {
        $controller = new PesananController();
        $items = [
            ['harga' => "lima ribu", 'jumlah' => 2]
        ];
        $hasil = $controller->kalkulasiTotalBelanja($items);
        $this->assertEquals("Error: Input harus berupa angka", $hasil);
    }
}