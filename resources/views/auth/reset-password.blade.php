@extends('layouts.auth')

@section('title', 'Perbarui Kata Sandi')

@section('content')
    <h2 class="auth-title">Perbarui Sandi</h2>
    <p style="text-align: center; margin-bottom: 1.5rem; color: #555;">
        Masukkan kata sandi baru Anda.
    </p>

    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        
        <!-- Password Baru -->
        <div class="form-group">
            <label for="password" class="form-label">Kata sandi baru:</label>
            <input type="password" id="password" name="password" required class="form-input @error('password') input-error @enderror">
            @error('password')
                <div class="error-message" style="color: red; font-size: 0.8rem;">{{ $message }}</div>
            @enderror
        </div>
        
        <!-- Konfirmasi Password Baru -->
        <div class="form-group">
            <label for="password_confirmation" class="form-label">Konfirmasi kata sandi:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required class="form-input">
        </div>
        
        <!-- Tampilkan Kata Sandi Checkbox -->
        <div class="form-group-check" style="margin-bottom: 1.5rem; display: flex; align-items: center;">
            <input type="checkbox" id="show-password" class="show-password" style="width: 16px; height: 16px; margin-right: 8px;">
            <label for="show-password" style="color: #555;">Tampilkan kata sandi</label>
        </div>

        <!-- Tombol Kirim -->
        <div class="form-group" style="margin-top: 2rem;">
            <button type="submit" class="btn-primary">
                Reset Kata Sandi
            </button>
        </div>
    </form>
@endsection