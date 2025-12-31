<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sandi Diperbarui - Gerai 3 Abdul</title>
    
    <!-- Kita copy CSS dari layouts/auth.blade.php, TAPI kita HAPUS .auth-card -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D4C0A0; /* Latar krem */
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
            max-width: 400px; /* Tetap responsive */
            text-align: center; /* Kita buat semua center */
        }
        .logo {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        .logo img {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        /* Ini adalah style untuk tombol 'LOGIN' */
        .btn-primary {
            width: 100%; /* Tombol lebar penuh */
            background-color: #B91C1C; /* Merah */
            color: white;
            padding: 0.8rem 1rem;
            border: none;
            border-radius: 0.5rem; /* Sesuai desain target, sedikit bulat */
            font-weight: bold;
            font-size: 1.1rem; /* Teks LOGIN lebih besar */
            cursor: pointer;
            text-decoration: none;
            display: block;
            box-sizing: border-box;
        }
        .btn-primary:hover {
            background-color: #991B1B; /* Merah lebih gelap */
        }

        /* Style untuk teks */
        .success-title {
            font-size: 1.75rem;
            line-height: 1.3;
            font-weight: bold;
            color: #333;
            margin-bottom: 0.5rem;
        }
        .success-subtitle {
            text-align: center;
            margin-bottom: 2rem;
            color: #555;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <!-- Logo -->
        <div class="logo">
            <img src="{{ asset('images/cafe.jpg') }}" alt="Logo Cafe 3 Abdul">
        </div>
        
        <!-- Konten (TANPA .auth-card) -->
        <h2 class="success-title">Kata sandi anda telah diperbarui</h2>
        
        <p class="success-subtitle">
            Silakan melakukan login
        </p>

        <!-- Tombol Login -->
        <div class="form-group">
            <a href="{{ route('login') }}" class="btn-primary">
                LOGIN
            </a>
        </div>
    </div>

</body>
</html>