<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Kasir Dashboard') - Gerai 3 Abdul</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }
        .admin-wrapper {
            display: flex;
            min-height: 100vh;
        }
        .sidebar {
            width: 260px;
            background-color: #B91C1C;
            color: white;
            display: flex;
            flex-direction: column; 
            min-height: 100vh; /* Setinggi viewport */
            
            /* --- PERBAIKAN PENTING --- */
            position: fixed; /* Kunci sidebar di tempatnya */
            top: 0;
            left: 0;
            z-index: 1000; /* Pastikan di atas konten lain */
            /* -------------------------- */
        }
        .sidebar-header {
            padding: 1rem;
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #B91C1C;
        }
        .sidebar-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }
        .sidebar-header h2 {
            margin: 0;
            font-size: 1.2rem;
        }
        .sidebar-nav {
            flex: 1; 
        }
        .nav-heading {
            padding: 1.25rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            background-color: #000; 
            color: white;
            border-bottom: 1px solid #991B1B;
        }
        .sidebar-nav a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 1.25rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-bottom: 1px solid #991B1B;
            transition: background-color 0.2s;
        }
        .sidebar-nav a:hover,
        .sidebar-nav a.active {
            background-color: #991B1B; /* Merah lebih gelap */
        }
        .sidebar-footer {
            padding: 1rem;
            margin-top: auto; /* KUNCI SOLUSI: Mendorong footer ke batas bawah */
        }
        .btn-logout {
            width: 100%;
            background-color: #333;
            color: white;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
        }
        .main-content {
            flex: 1; 
            padding: 2rem;
            /* --- PERBAIKAN PENTING --- */
            margin-left: 260px;
        }
        .content-header {
            font-size: 1.75rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 1.5rem;
            border-bottom: 2px solid #ddd;
            padding-bottom: 1rem;
        }
        .content-body {
            background-color: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('images/cafe.jpg') }}" alt="Logo">
                <h2>Cafe Gerai 3 Abdul</h2>
            </div>
            <nav class="sidebar-nav">
                <center><div class="nav-heading">Menu</div></center>
                
                <a href="{{ route('kasir.dashboard') }}" 
                   class="{{ request()->routeIs('kasir.dashboard') ? 'active' : '' }}">
                   Pemesanan pelanggan
                </a>
                <a href="{{ route('kasir.diproses') }}" 
                   class="{{ request()->routeIs('kasir.diproses') ? 'active' : '' }}">
                   Pesanan yang diproses
                </a>
                <a href="{{ route('kasir.riwayat') }}" 
                   class="{{ request()->routeIs('kasir.riwayat') ? 'active' : '' }}">
                   Riwayat pemesanan
                </a>

            </nav>
            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">Keluar</button>
                </form>
            </div>
        </aside>

        <main class="main-content">
            <h1 class="content-header">
                @yield('header_title') </h1>
            <div class="content-body">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('scripts')
</body>
</html>