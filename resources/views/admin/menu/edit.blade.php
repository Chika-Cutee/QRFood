@extends('layouts.admin')

@section('title', 'Edit Menu')
@section('header_title', 'Edit Menu')

@push('styles')
<style>
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    .form-input, .form-select {
        width: 100%;
        padding: 0.8rem 1rem;
        border: 1px solid #BDBDBD;
        border-radius: 0.5rem;
        box-sizing: border-box; /* Penting */
        font-size: 1rem;
    }
    .btn-submit {
        background-color: #2563EB; /* Biru (Update) */
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
    }
    .btn-cancel {
        background-color: #6B7280; /* Abu-abu */
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: bold;
        text-decoration: none;
    }
    /* Untuk error validasi */
    .input-error {
        border-color: #DC2626; /* Merah */
    }
    .error-message {
        color: #DC2626;
        font-size: 0.9rem;
        margin-top: 0.25rem;
    }
</style>
@endpush

@section('content')
    <form action="{{ route('admin.menu.update', $produk->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') <div class="form-group">
            <label for="name" class="form-label">Nama Menu</label>
            <input type="text" id="name" name="name" class="form-input @error('name') input-error @enderror" value="{{ old('name', $produk->name) }}">
            @error('name')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="price" class="form-label">Harga</label>
            <input type="number" id="price" name="price" class="form-input @error('price') input-error @enderror" value="{{ old('price', $produk->price) }}">
            @error('price')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="category" class="form-label">Kategori</label>
            <select id="category" name="category" class="form-select @error('category') input-error @enderror">
                <option value="" disabled>-- Pilih Kategori --</option>
                <option value="makanan" {{ old('category', $produk->category) == 'makanan' ? 'selected' : '' }}>Makanan</option>
                <option value="minuman" {{ old('category', $produk->category) == 'minuman' ? 'selected' : '' }}>Minuman</option>
                <option value="paket" {{ old('category', $produk->category) == 'paket' ? 'selected' : '' }}>Paket</option>
            </select>
            @error('category')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Gambar Saat Ini:</label>
            <img src="{{ asset($produk->image_url) }}" alt="{{ $produk->name }}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 5px;">
        </div>

        <div class="form-group">
            <label for="image" class="form-label">Upload Gambar Baru (Opsional)</label>
            <input type="file" id="image" name="image" class="form-input @error('image') input-error @enderror">
            <small style="color: #6B7280;">*Kosongkan jika tidak ingin mengganti gambar.*</small>
            @error('image')
                <div class="error-message">{{ $message }}</div>
            @enderror
        </div>
        
        <div>
            <button type="submit" class="btn-submit">Update Menu</button>
            <a href="{{ route('admin.menu.index') }}" class="btn-cancel">Batal</a>
        </div>
    </form>
@endsection