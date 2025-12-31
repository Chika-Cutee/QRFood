<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetOtp extends Model
{
    use HasFactory;

    // Nama tabelnya, sesuai migrasi Anda
    protected $table = 'password_reset_otps';

    // Kolom yang boleh diisi
    protected $fillable = [
        'email',
        'otp',
        'expires_at',
    ];

    // Otomatis ubah 'expires_at' menjadi objek Carbon
    protected $casts = [
        'expires_at' => 'datetime',
    ];
}