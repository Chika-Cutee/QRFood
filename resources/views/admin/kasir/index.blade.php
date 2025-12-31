@extends('layouts.admin')

@section('title', 'Kelola Akun Kasir')
@section('header_title', 'Kelola Akun Kasir')

@push('styles')
<style>
    /* Style untuk daftar kasir */
    .kasir-list {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .kasir-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        background-color: white;
        border: 2px solid #ddd;
        border-radius: 1rem;
        cursor: pointer;
        transition: border-color 0.2s, background-color 0.2s;
    }
    .kasir-item:hover {
        border-color: #B91C1C;
    }
    .kasir-item.selected {
        border-color: #B91C1C; /* Warna merah */
        background-color: #FEE2E2; /* Latar merah muda */
    }
    .kasir-item span {
        font-size: 1.1rem;
        font-weight: 600;
        color: #333;
    }
    
    /* Style untuk tombol aksi */
    .action-buttons {
        display: flex;
        gap: 1rem;
    }
    .btn-action {
        padding: 10px 25px;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: bold;
        color: white;
        background-color: #B91C1C; /* Merah */
        text-decoration: none;
        cursor: pointer;
    }
    /* Style untuk tombol yang non-aktif */
    .btn-action.disabled {
        background-color: #999;
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none; /* Mencegah klik */
    }
</style>
@endpush

@section('content')

    @if (session('success'))
        <div style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <div class="kasir-list" id="kasirListContainer">
        @forelse ($kasirs as $index => $kasir)
            <div class="kasir-item" 
                 data-id="{{ $kasir->id }}"
                 data-edit-url="{{ route('admin.kasir.edit', $kasir->id) }}"
                 data-destroy-url="{{ route('admin.kasir.destroy', $kasir->id) }}">
                <span>Kasir {{ $index + 1 }}</span>
                <span>{{ $kasir->name }}</span>
            </div>
        @empty
            <p style="text-align: center;">Belum ada akun kasir.</p>
        @endforelse
    </div>

    <div class="action-buttons">
        <a href="{{ route('admin.kasir.create') }}" class="btn-action">Tambah Akun</a>
        
        <a href="#" id="editButton" class="btn-action disabled">Edit Akun</a>
        
        <form id="deleteForm" action="#" method="POST" style="margin: 0;">
            @csrf
            @method('DELETE')
            <button type="submit" id="deleteButton" class="btn-action disabled" onclick="return confirm('Yakin ingin menghapus akun kasir ini?')">
                Hapus Akun
            </button>
        </form>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const kasirItems = document.querySelectorAll('.kasir-item');
        const editButton = document.getElementById('editButton');
        const deleteButton = document.getElementById('deleteButton');
        const deleteForm = document.getElementById('deleteForm');

        kasirItems.forEach(item => {
            item.addEventListener('click', function () {
                // 1. Hapus 'selected' dari semua item
                kasirItems.forEach(i => i.classList.remove('selected'));
                
                // 2. Tambahkan 'selected' ke item yang diklik
                this.classList.add('selected');

                // 3. Ambil data URL dari item
                const editUrl = this.dataset.editUrl;
                const destroyUrl = this.dataset.destroyUrl;

                // 4. Aktifkan tombol dan set URL-nya
                editButton.classList.remove('disabled');
                editButton.href = editUrl;
                
                deleteButton.classList.remove('disabled');
                deleteForm.action = destroyUrl;
            });
        });
    });
</script>
@endpush