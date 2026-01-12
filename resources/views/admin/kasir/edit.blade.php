@extends('layouts.admin')

@section('title', 'Edit Akun Kasir')
@section('header_title', 'Edit Akun Kasir')

@push('styles')
<style>
    .form-container { max-width: 600px; margin: 0 auto; padding: 1.5rem; background-color: #f9f9f9; border-radius: 8px; }
    .form-group { margin-bottom: 1.5rem; display: flex; align-items: center; }
    .form-label { width: 120px; font-weight: 600; font-size: 1.1rem; }
    .form-input { flex: 1; padding: 0.8rem 1rem; border: 1px solid #BDBDBD; border-radius: 2rem; box-sizing: border-box; font-size: 1rem; }
    .btn-submit { background-color: #B91C1C; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 1rem; font-weight: bold; cursor: pointer; display: block; margin-left: auto; }
    .input-error { border-color: #DC2626; }
    .error-message { color: #DC2626; font-size: 0.9rem; margin-top: 0.25rem; padding-left: 120px; }

    @media (max-width: 480px) {
        .form-group {
            flex-direction: column;
            align-items: flex-start;
        }
        .form-label {
            width: auto;
            margin-bottom: 0.5rem;
        }
        .error-message {
            padding-left: 0;
        }
        .form-container {
            padding: 1rem;
        }
    }
</style>
@endpush

@section('content')
    <div class="form-container">
        <form action="{{ route('admin.kasir.update', $kasir->id) }}" method="POST">
            @csrf
            @method('PUT') <div class="form-group">
                <label for="email" class="form-label">Email :</label>
                <input type="email" id="email" name="email" class="form-input @error('email') input-error @enderror" value="{{ old('email', $kasir->email) }}">
            </div>
            @error('email') <div class="error-message">{{ $message }}</div> @enderror

            <div class="form-group">
                <label for="name" class="form-label">Username :</label>
                <input type="text" id="name" name="name" class="form-input @error('name') input-error @enderror" value="{{ old('name', $kasir->name) }}">
            </div>
            @error('name') <div class="error-message">{{ $message }}</div> @enderror
            
            <hr style="border-top: 1px solid #ddd; margin: 2rem 0;">
            <p style="text-align: center; color: #555;">Kosongkan password jika tidak ingin mengubahnya.</p>

            <div class="form-group">
                <label for="password" class="form-label">Password :</label>
                <input type="password" id="password" name="password" class="form-input @error('password') input-error @enderror">
            </div>
            @error('password') <div class="error-message">{{ $message }}</div> @enderror

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Password :</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-input">
            </div>
            
            <div>
                <button type="submit" class="btn-submit">Update Akun</button>
            </div>
        </form>
    </div>
@endsection