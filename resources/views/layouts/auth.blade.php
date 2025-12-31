<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Gerai 3 Abdul</title>
    
    <!-- CSS Sederhana (Pengganti Tailwind) -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D4C0A0; /* Warna krem/coklat dari desain */
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
            box-sizing: border-box;
        }
        .auth-container {
            width: 100%;
            max-width: 400px; /* Lebar form */
        }
        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo img {
            width: 160px; /* 10rem */
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .auth-card {
            background-color: #F0E6D8; /* Warna krem muda */
            padding: 1.5rem 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
        }
        .auth-title {
            text-align: center;
            font-size: 1.75rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1.25rem;
        }
        .form-group label, .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #555;
        }
        .form-input {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #BDBDBD;
            border-radius: 0.5rem;
            box-sizing: border-box; /* Penting untuk padding */
            font-size: 1rem;
        }
        .form-input:focus {
            outline: none;
            border-color: #900;
            box-shadow: 0 0 0 2px rgba(185, 28, 28, 0.2);
        }
        .btn-primary {
            width: 100%;
            background-color: #B91C1C; /* Merah (bg-red-700) */
            color: white;
            padding: 0.8rem 1rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-primary:hover {
            background-color: #991B1B; /* Merah lebih gelap (bg-red-800) */
        }
        .auth-link {
            text-align: center;
            font-size: 0.9rem;
            color: #666;
            margin-top: 1.5rem;
        }
        .auth-link a {
            color: #581C87; /* Ungu (purple-700) */
            text-decoration: none;
            font-weight: 600;
        }
        .auth-link a:hover {
            text-decoration: underline;
        }
        .error-message {
            background-color: #FEE2E2;
            border: 1px solid #FCA5A5;
            color: #B91C1C;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
    @stack('styles') <!-- Untuk CSS tambahan (seperti halaman OTP) -->
</head>
<body>

    <div class="auth-container">
        <!-- Logo -->
        <div class="logo">
            <img src="{{ asset('images/cafe.jpg') }}" alt="Logo Cafe 3 Abdul">
        </div>
        
        <!-- Card Konten -->
        <div class="auth-card">
            @yield('content')
        </div>
    </div>

    <!-- ▼▼▼ SCRIPT YANG DIPERBAIKI ADA DI SINI ▼▼▼ -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const showPasswordCheckboxes = document.querySelectorAll('.show-password');

            showPasswordCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    
                    // --- INI KODE YANG LAMA (SALAH) ---
                    // const passwordInput = this.closest('form').querySelector('input[type="password"], input[type="text"]');
                    
                    // --- INI KODE YANG BARU (BENAR) ---
                    // Kode ini akan mencari SEMUA input dengan nama 'password' atau 'password_confirmation'
                    // di dalam form yang sama dengan checkbox.
                    const passwordInputs = this.closest('form').querySelectorAll('input[name="password"], input[name="password_confirmation"]');
                    
                    // Ganti tipe setiap input yang ditemukan
                    passwordInputs.forEach(input => {
                        if (this.checked) {
                            input.type = 'text';
                        } else {
                            input.type = 'password';
                        }
                    });
                });
            });
        });
    </script>
    @stack('scripts') <!-- Untuk JS tambahan (seperti halaman OTP) -->
</body>
</html>