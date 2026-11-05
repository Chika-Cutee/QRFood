<?php

namespace App\Services;

use Midtrans\Config;

/**
 * Satu-satunya tempat konfigurasi Midtrans.
 *
 * [Pedoman Komputasional - Duplikasi rutinitas]
 * Sebelumnya 4 baris konfigurasi yang sama disalin di PesananController (3x)
 * dan Api\OrderController (1x). Sekarang cukup memanggil MidtransService::configure().
 */
class MidtransService
{
    public static function configure(): void
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }
}
