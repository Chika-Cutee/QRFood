@extends('layouts.auth')

@section('title', 'Registrasi')

@section('content')
    <h2 class="auth-title">Registrasi</h2>

    <form method="POST" action="{{ route('register.post') }}">
        @csrf
        
        <div class="form-group">
            <label for="name">Nama pengguna:</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus class="form-input">
            @error('name') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>
        
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-input">
            @error('email') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="password">Kata sandi:</label>
            <input type="password" id="password" name="password" required class="form-input">
            @error('password') <span style="color: red; font-size: 0.8rem;">{{ $message }}</span> @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi kata sandi:</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required class="form-input">
        </div>
        
        <div class="form-group" style="margin-top: 2rem;">
            <button type="submit" class="btn-primary">
                Daftar &rarr;
            </button>
        </div>
        
        <p class="auth-link">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
        </p>
    </form>
@endsection