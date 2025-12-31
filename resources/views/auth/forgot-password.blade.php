@extends('layouts.auth')

@section('title', 'Lupa Kata Sandi')

@section('content')
    <!-- ▼▼▼ PERUBAHAN TEKS DI SINI ▼▼▼ -->
    <h2 class="auth-title">Lupa kata sandi?</h2>

    <!-- Menampilkan pesan sukses (status) -->
    @if (session('status'))
        <div class="success-message" style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem; text-align: center;">
            {{ session('status') }}
        </div>
    @endif
    
    @if ($errors->any())
        <div class="error-message">
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <p style="text-align: center; margin-bottom: 1.5rem; color: #555;">
        Masukkan email untuk perbarui kata sandi anda
    </p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        
        <!-- Email -->
        <div class="form-group">
            <label for="email" class="form-label">Masukkan email:</label>
            <!-- Tambah placeholder -->
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus class="form-input" placeholder="contoh@email.com">
        </div>
        
        <!-- Tombol Kirim -->
        <div class="form-group" style="margin-top: 2rem;">
            <!-- Ganti teks tombol -->
            <button type="submit" class="btn-primary">
                KIRIM
            </button>
        </div>
        
        <!-- Link Kembali ke Login -->
        <p class="auth-link">
            <a href="{{ route('login') }}">Kembali ke halaman login</a>
        </p>
    </form>
    <!-- ▲▲▲ AKHIR PERUBAHAN ▲▲▲ -->
@endsection