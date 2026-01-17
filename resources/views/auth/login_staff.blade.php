@extends('layouts.auth')

@section('title', 'Login Staff')

@section('content')
    <h2 class="auth-title">Login Staff</h2>

    @if ($errors->any())
        <div class="error-message">
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    <form method="POST" action="{{ route('login.staff.post') }}">
        @csrf
        
        <div class="form-group">
            <label for="email">Nama pengguna/email:</label>
            <input type="text" id="email" name="email" value="{{ old('email') }}" required autofocus class="form-input">
        </div>
        
        <!-- Password -->
        <div class="form-group">
            <label for="password">Kata sandi:</label>
            <input type="password" id="password" name="password" required class="form-input">
        </div>

        <!-- Tampilkan kata sandi -->
        <div class="form-group-check" style="margin-bottom: 1.5rem; display: flex; align-items: center;">
            <input type="checkbox" id="show-password" class="show-password" style="width: 16px; height: 16px; margin-right: 8px;">
            <label for="show-password" style="color: #555;">Tampilkan kata sandi</label>
        </div> 

        <!-- Tombol Login -->
        <div class="form-group" style="margin-top: 2rem;">
            <button type="submit" class="btn-primary">
                Login &rarr;
            </button>
        </div>
        
    </form>
@endsection
