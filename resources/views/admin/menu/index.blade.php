@extends('layouts.admin')

@section('title', 'Kelola Menu')
@section('header_title', 'Kelola Menu')

@push('styles')
<style>
    .tab-container {
        width: 100%;
    }
    .tab-buttons {
        display: flex;
        border-bottom: 2px solid #ddd;
    }
    .tab-btn {
        padding: 10px 20px;
        cursor: pointer;
        background: #f0f0f0;
        border: none;
        border-bottom: 2px solid transparent;
        font-size: 1.1rem;
        font-weight: 600;
        margin-right: 5px;
        border-radius: 5px 5px 0 0;
    }
    .tab-btn.active {
        background: white;
        border-top: 2px solid #B91C1C;
        border-left: 2px solid #B91C1C;
        border-right: 2px solid #B91C1C;
        border-bottom: 2px solid white;
        position: relative;
        top: 2px;
    }
    .tab-content {
        display: none;
        padding-top: 1.5rem;
    }
    .tab-content.active {
        display: block;
    }

    .menu-table {
        width: 100%;
        border-collapse: collapse;
    }
    .menu-table th, .menu-table td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }
    .menu-table th {
        background-color: #f9f9f9;
        font-weight: bold;
    }
    .menu-table img {
        width: 60px;
        height: 60px;
        border-radius: 5px;
        object-fit: cover;
    }
    .actions {
        display: flex;
        gap: 5px;
    }
    .btn-edit, .btn-delete {
        padding: 5px 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        color: white;
        font-weight: bold;
    }
    .btn-edit { background-color: #2563EB; }
    .btn-delete { background-color: #DC2626; }

    .btn-add-menu {
        background-color: #16A34A;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        font-weight: bold;
        text-decoration: none;
        margin-bottom: 1.5rem;
        display: inline-block;
    }
    .table-wrapper {
        overflow-x: auto;
        width: 100%;
    }
</style>
@endpush

@section('content')
    @if (session('success'))
        <div style="background-color: #D1FAE5; color: #065F46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1rem;">
            {{ session('success') }}
        </div>
    @endif

    <a href="{{ route('admin.menu.create') }}" class="btn-add-menu">Tambah Menu</a>

    <div class="tab-container">
        <div class="tab-buttons">
            <button class="tab-btn active" data-tab="makanan">Makanan</button>
            <button class="tab-btn" data-tab="minuman">Minuman</button>
            <button class="tab-btn" data-tab="paket">Paket</button>
        </div>

        {{-- ======================= MAKANAN ======================= --}}
        <div id="makanan" class="tab-content active">
        <div class="table-wrapper">
            <table class="menu-table">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Menu</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kategoriProduk['makanan'] as $produk)
                    <tr>
                        <td><img src="{{ asset($produk->image_url) }}" alt="{{ $produk->name }}"></td>
                        <td>{{ $produk->name }}</td>
                        <td>Rp {{ number_format($produk->price, 0, ',', '.') }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.menu.edit', $produk->id) }}" class="btn-edit">Edit</a>
                            <form action="{{ route('admin.menu.destroy', $produk->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('Yakin ingin menghapus menu ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">Belum ada menu makanan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>

        {{-- ======================= MINUMAN ======================= --}}
        <div id="minuman" class="tab-content">
        <div class="table-wrapper">
            <table class="menu-table">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Menu</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kategoriProduk['minuman'] as $produk)
                    <tr>
                        <td><img src="{{ asset($produk->image_url) }}" alt="{{ $produk->name }}"></td>
                        <td>{{ $produk->name }}</td>
                        <td>Rp {{ number_format($produk->price, 0, ',', '.') }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.menu.edit', $produk->id) }}" class="btn-edit">Edit</a>
                            <form action="{{ route('admin.menu.destroy', $produk->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('Yakin ingin menghapus menu ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">Belum ada menu minuman.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>

        {{-- ======================= PAKET ======================= --}}
        <div id="paket" class="tab-content">
        <div class="table-wrapper">
            <table class="menu-table">
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Nama Menu</th>
                        <th>Harga</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kategoriProduk['paket'] as $produk)
                    <tr>
                        <td><img src="{{ asset($produk->image_url) }}" alt="{{ $produk->name }}"></td>
                        <td>{{ $produk->name }}</td>
                        <td>Rp {{ number_format($produk->price, 0, ',', '.') }}</td>
                        <td class="actions">
                            <a href="{{ route('admin.menu.edit', $produk->id) }}" class="btn-edit">Edit</a>
                            <form action="{{ route('admin.menu.destroy', $produk->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('Yakin ingin menghapus menu ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align: center;">Belum ada menu paket.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));

                button.classList.add('active');
                document.getElementById(button.dataset.tab).classList.add('active');
            });
        });
    });
</script>
@endpush
