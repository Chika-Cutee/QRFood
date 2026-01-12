<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Koki Dashboard') - Gerai 3 Abdul</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f2f5; margin: 0; padding: 0; overflow-x: hidden; /* Mencegah scroll horizontal */ }
        .admin-wrapper { display: flex; min-height: 100vh; align-items: flex-start; }
        .sidebar {
            width: 260px; background-color: #B91C1C; /* Merah untuk Koki */
            color: white; display: flex; flex-direction: column;
            position: fixed; top: 0; left: 0; z-index: 1000; height: 100vh;
            transition: transform 0.3s ease-in-out;
            transform: translateX(-100%);
        }
        .sidebar.open {
            transform: translateX(0);
        }
        .sidebar-header { padding: 1rem; display: flex; align-items: center; gap: 10px; background-color: #B91C1C; }
        .sidebar-header img { width: 50px; height: 50px; border-radius: 50%; }
        .sidebar-header h2 { margin: 0; font-size: 1.2rem; }
        .sidebar-nav { flex: 1; }
        .nav-heading { padding: 1.25rem 1.5rem; font-size: 1.1rem; font-weight: 600; background-color: #000; color: white; border-bottom: 1px solid #172554; }
        .sidebar-nav a { display: block; color: white; text-decoration: none; padding: 1.25rem 1.5rem; font-size: 1.1rem; font-weight: 600; border-bottom: 1px solid #172554; transition: background-color 0.2s; }
        .sidebar-nav a:hover, .sidebar-nav a.active { background-color: #B91C1C; }
        .sidebar-footer { padding: 1rem; margin-top: auto; }
        .btn-logout { width: 100%; background-color: #333; color: white; border: none; padding: 1rem; border-radius: 8px; font-size: 1rem; font-weight: bold; cursor: pointer; }
        .main-content {
            flex: 1;
            transition: margin-left 0.3s ease-in-out;
            overflow-x: hidden; /* Paksa agar tidak ada scroll horizontal */
            padding: 0;
        }
        .content-header { display: none; }
        .content-body {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0;
            box-shadow: none;
        }
        /* Card Style */
        .order-card { border: 1px solid #ddd; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; background-color: #fff; }
        .order-header { display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 0.5rem; margin-bottom: 0.5rem; font-weight: bold; }
        .btn-selesai { background-color: #16A34A; color: white; padding: 0.5rem 1rem; text-decoration: none; border-radius: 4px; display: inline-block; margin-top: 10px; }
        .btn-selesai:hover { background-color: #15803D; }

        /* Responsive Styles */
        .mobile-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 900;
        }
        #hamburger-btn {
            font-size: 1.5rem;
            background: none;
            border: none;
            cursor: pointer;
        }
        .mobile-header-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #333;
        }
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
        }
        .overlay.active {
            display: block;
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="overlay" id="overlay"></div>
    <div class="admin-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="{{ asset('images/cafe.jpg') }}" alt="Logo">
                <h2>Cafe Gerai 3 Abdul</h2>
            </div>
            <nav class="sidebar-nav">
                <center><div class="nav-heading">Menu Dapur</div></center>
                <center><a href="{{ route('koki.dashboard') }}" class="active">Pesanan Pelanggan</a></center>
            </nav>
            <div class="sidebar-footer">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">Keluar</button>
                </form>
            </div>
        </aside>
        <main class="main-content">
            <header class="mobile-header">
                <button id="hamburger-btn">&#9776;</button>
                <div class="mobile-header-title">@yield('header_title')</div>
            </header>
            <h1 class="content-header">@yield('header_title')</h1>
            <div class="content-body">
                @if(session('success'))
                    <div style="background: #d1fae5; color: #065f46; padding: 1rem; margin-bottom: 1rem; border-radius: 0.5rem;">{{ session('success') }}</div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const hamburgerBtn = document.getElementById('hamburger-btn');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            function closeSidebar() {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
            }

            hamburgerBtn.addEventListener('click', function () {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            });

            overlay.addEventListener('click', function () {
                closeSidebar();
            });
        });
    </script>
</body>
</html>
