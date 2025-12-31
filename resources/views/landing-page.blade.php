<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QRFood - Sistem Pemesanan Menu Berbasis QR Barcode</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
        }

        /* Header */
        header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(168, 32, 26, 0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: all 0.3s;
        }

        header.scrolled {
            box-shadow: 0 4px 30px rgba(168, 32, 26, 0.15);
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 5%;
            max-width: 1400px;
            margin: 0 auto;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: logoFloat 3s ease-in-out infinite;
        }

        .logo img {
            height: 50px;
            width: 50px; /* Pastikan rasio kotak agar bulat sempurna */
            object-fit: cover;
            border-radius: 50%; /* Membuat logo menjadi bulat */
        }

        @keyframes logoFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        .nav-btn {
            background: #A8201A;
            color: white;
            padding: 0.7rem 1.8rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(168, 32, 26, 0.2);
            position: relative;
            overflow: hidden;
        }

        .nav-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .nav-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .nav-btn:hover {
            background: #8a1a16;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(168, 32, 26, 0.3);
        }

        /* Hero Section */
        .hero {
            margin-top: 80px;
            padding: 5rem 5% 4rem;
            /* Background Image dengan Overlay agar teks tetap terbaca */
            background: linear-gradient(135deg, rgba(168, 32, 26, 0.9) 0%, rgba(109, 21, 17, 0.95) 100%), url("{{ asset('images/wallpaper.jpeg') }}");
            background-size: cover;
            background-position: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(255, 255, 255, 0.03) 10px,
                rgba(255, 255, 255, 0.03) 20px
            );
            animation: movePattern 20s linear infinite;
        }

        @keyframes movePattern {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .hero-text h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            animation: fadeInUp 0.8s ease;
            line-height: 1.2;
        }

        .hero-text p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            animation: fadeInUp 0.8s ease 0.2s backwards;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            animation: fadeInUp 0.8s ease 0.4s backwards;
        }

        .btn-primary {
            background: #F5E1C0;
            color: #A8201A;
            padding: 1rem 2rem;
            border: none;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-primary::after {
            content: '→';
            position: absolute;
            right: 20px;
            opacity: 0;
            transition: all 0.3s;
        }

        .btn-primary:hover::after {
            opacity: 1;
            right: 15px;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(245, 225, 192, 0.3);
            padding-right: 3rem;
        }

        .btn-secondary {
            background: transparent;
            color: white;
            padding: 1rem 2rem;
            border: 2px solid white;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: white;
            color: #A8201A;
        }

        .hero-image {
            text-align: center;
            animation: fadeInRight 0.8s ease;
        }

        .phone-mockup {
            width: 300px; /* Mengatur ukuran gambar agar tidak kebesaran (setara visual emoji lama) */
            height: auto;
            opacity: 0.9;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* Cafe Partner Section */
        .cafe-partner {
            padding: 5rem 5%;
            background: #F5E1C0;
            position: relative;
            overflow: hidden;
        }

        .cafe-partner::before {
            content: '☕';
            position: absolute;
            font-size: 20rem;
            opacity: 0.05;
            top: -50px;
            right: -50px;
            animation: rotate 30s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .cafe-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
            position: relative;
        }

        .cafe-image {
            background: linear-gradient(135deg, rgba(168, 32, 26, 0.1) 0%, rgba(168, 32, 26, 0.05) 100%);
            border-radius: 20px;
            height: 400px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(168, 32, 26, 0.3);
            transition: transform 0.3s;
        }

        .cafe-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 20px;
        }

        .cafe-image:hover {
            transform: scale(1.05) rotate(-2deg);
        }

        .cafe-info h2 {
            font-size: 2.5rem;
            color: #A8201A;
            margin-bottom: 1.5rem;
        }

        .cafe-info p {
            font-size: 1.1rem;
            color: #333;
            line-height: 1.8;
            margin-bottom: 1.5rem;
        }

        .location-box {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            border-left: 4px solid #A8201A;
            box-shadow: 0 5px 15px rgba(168, 32, 26, 0.1);
            margin-top: 2rem;
        }

        .location-box h3 {
            color: #A8201A;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .location-box p {
            margin: 0;
            color: #666;
        }

        /* About Section */
        .about {
            padding: 5rem 5%;
            background: white;
        }

        .about-content {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .about h2 {
            font-size: 2.5rem;
            color: #A8201A;
            margin-bottom: 2rem;
        }

        .about p {
            font-size: 1.1rem;
            color: #333;
            max-width: 800px;
            margin: 0 auto 3rem;
            line-height: 1.8;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .stat-card {
            background: linear-gradient(135deg, #F5E1C0 0%, #e8d4ab 100%);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(168, 32, 26, 0.1);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(168, 32, 26, 0.2);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #A8201A;
            margin-bottom: 0.5rem;
            animation: countUp 2s ease-out;
        }

        @keyframes countUp {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }

        .stat-label {
            font-size: 1rem;
            color: #666;
        }

        /* Features Section */
        .features {
            padding: 5rem 5%;
            background: linear-gradient(to bottom, white 0%, #F5E1C0 100%);
        }

        .features-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .features h2 {
            text-align: center;
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #A8201A;
        }

        .features-subtitle {
            text-align: center;
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 3rem;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .feature-card {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            border: 2px solid transparent;
            text-align: center;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(168, 32, 26, 0.1), transparent);
            transition: left 0.5s;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: #A8201A;
            box-shadow: 0 10px 30px rgba(168, 32, 26, 0.15);
        }

        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            display: inline-block;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .feature-card:hover .feature-icon {
            animation: shake 0.5s;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .feature-card h3 {
            margin-bottom: 1rem;
            color: #A8201A;
            font-size: 1.4rem;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Reviews Section */
        .reviews {
            padding: 5rem 5%;
            background: white;
            position: relative;
            overflow: hidden;
        }

        .reviews-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .reviews h2 {
            text-align: center;
            font-size: 2.5rem;
            color: #A8201A;
            margin-bottom: 3rem;
        }

        .reviews-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .review-card {
            background: #F5E1C0;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(168, 32, 26, 0.1);
            transition: all 0.3s;
            position: relative;
        }

        .review-card::before {
            content: '"';
            position: absolute;
            top: -20px;
            left: 20px;
            font-size: 5rem;
            color: #A8201A;
            opacity: 0.2;
        }

        .review-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(168, 32, 26, 0.2);
        }

        .review-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .review-avatar {
            width: 50px;
            height: 50px;
            background: #A8201A;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .review-info h4 {
            color: #A8201A;
            margin-bottom: 0.2rem;
        }

        .review-stars {
            color: #FFD700;
            font-size: 1.2rem;
        }

        .review-text {
            color: #333;
            line-height: 1.6;
            font-style: italic;
        }

        /* Review Form Styles */
        .review-form-container {
            max-width: 600px;
            margin: 0 auto 3rem;
        }

        .review-form {
            background: white;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(168, 32, 26, 0.1);
        }

        .review-form h3 {
            color: #A8201A;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #F5E1C0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #A8201A;
            box-shadow: 0 0 0 3px rgba(168, 32, 26, 0.1);
        }

        .star-rating {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .star-rating .star {
            font-size: 2rem;
            cursor: pointer;
            transition: all 0.3s;
            filter: grayscale(100%);
        }

        .star-rating .star.active {
            filter: grayscale(0%);
            transform: scale(1.2);
        }

        .star-rating .star:hover {
            transform: scale(1.3);
        }

        .submit-review-btn {
            width: 100%;
            background: #A8201A;
            color: white;
            padding: 1rem;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .submit-review-btn:hover {
            background: #8a1a16;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(168, 32, 26, 0.3);
        }

        .empty-reviews {
            text-align: center;
            padding: 3rem;
            color: #666;
        }

        .empty-reviews-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Target Section */
        .target {
            padding: 5rem 5%;
            background: linear-gradient(135deg, #F5E1C0 0%, #e8d4ab 100%);
        }

        .target-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .target h2 {
            text-align: center;
            font-size: 2.5rem;
            color: #A8201A;
            margin-bottom: 3rem;
        }

        .target-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .target-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(168, 32, 26, 0.1);
            transition: all 0.3s;
        }

        .target-card:hover {
            transform: scale(1.05);
        }

        .target-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #A8201A 0%, #6d1511 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 1.5rem;
            transition: all 0.3s;
        }

        .target-card:hover .target-icon {
            transform: rotate(360deg);
        }

        .target-card h3 {
            color: #A8201A;
            margin-bottom: 1rem;
        }

        /* Team Section */
        .team {
            padding: 5rem 5%;
            background: white;
        }

        .team-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .team h2 {
            text-align: center;
            font-size: 2.5rem;
            color: #A8201A;
            margin-bottom: 3rem;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .team-card {
            background: #F5E1C0;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s;
        }

        .team-card:hover {
            transform: translateY(-10px) rotate(2deg);
        }

        .team-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #A8201A 0%, #6d1511 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin: 0 auto 1.5rem;
            font-weight: bold;
            transition: all 0.3s;
        }

        .team-card:hover .team-avatar {
            transform: scale(1.1);
        }

        .team-name {
            font-size: 1.3rem;
            font-weight: bold;
            color: #A8201A;
            margin-bottom: 0.5rem;
        }

        .team-id {
            color: #666;
            margin-bottom: 1rem;
        }

        .team-role {
            background: #A8201A;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            display: inline-block;
        }

        /* CTA Section */
        .cta {
            padding: 5rem 5%;
            background: linear-gradient(135deg, #A8201A 0%, #6d1511 100%);
            color: white;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .cta::before {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            top: -250px;
            right: -250px;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .cta p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        /* Footer */
        footer {
            background: #1a0a09;
            color: white;
            padding: 3rem 5%;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-section h3 {
            color: #F5E1C0;
            margin-bottom: 1rem;
        }

        .footer-section p, .footer-section li {
            opacity: 0.8;
            line-height: 1.8;
        }

        .footer-section ul {
            list-style: none;
        }

        .footer-bottom {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid rgba(245, 225, 192, 0.2);
            opacity: 0.8;
        }

        /* Scroll to top button */
        .scroll-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #A8201A;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transition: all 0.3s;
            z-index: 999;
            box-shadow: 0 4px 15px rgba(168, 32, 26, 0.3);
        }

        .scroll-top.visible {
            opacity: 1;
        }

        .scroll-top:hover {
            background: #8a1a16;
            transform: translateY(-5px);
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive */
        @media (max-width: 968px) {
            .hero-content, .cafe-content {
                grid-template-columns: 1fr;
                text-align: center;
            }

            .hero-text h1, .cafe-info h2 {
                font-size: 2.2rem;
            }

            .hero-buttons {
                justify-content: center;
            }

            .features h2, .about h2, .target h2, .team h2, .reviews h2 {
                font-size: 2rem;
            }

            .phone-mockup {
                width: 200px; /* Ukuran responsif untuk layar kecil */
            }

            .cafe-image {
                height: 300px;
                font-size: 6rem;
            }
        }

        @media (max-width: 768px) {
            .hero-buttons {
                flex-direction: column;
            }

            .btn-primary, .btn-secondary {
                width: 100%;
            }

            .feature-grid, .reviews-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <<header id="header">
        <nav>
            <div class="logo">
                <img src="{{ asset('images/logo.jpeg') }}" alt="QRFood Logo">
            </div>
            <center><h1 ><p style="margin-top: 0.5rem; color: #A8201A;">#Scan To Order</h1></center>
            <!-- Tambahkan tombol Login disini -->
           <!-- <a href="{{ route('login') }}" class="nav-btn" style="text-decoration: none;">Login</a> -->
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Sistem Pemesanan Menu Berbasis QR Barcode</h1>
                <p>Solusi modern untuk meningkatkan efisiensi pelayanan restoran dan kafe Anda. Pangkas waktu antrian hingga 50% dan minimalisir kesalahan pencatatan!</p>
                <div class="hero-buttons">
                    <button class="btn-primary" onclick="document.querySelector('#fitur').scrollIntoView({behavior: 'smooth'})">Lihat Fitur</button>
                    <button class="btn-secondary" onclick="document.querySelector('#cafe').scrollIntoView({behavior: 'smooth'})">Pelajari Lebih</button>
                </div>
            </div>
            <div class="hero-image">
                <!-- Gambar Phone Mockup -->
                <img src="{{ asset('images/phone.png') }}" alt="Tampilan Aplikasi" class="phone-mockup">
            </div>
        </div>
    </section>

    <!-- Cafe Partner Section -->
    <section class="cafe-partner" id="cafe">
        <div class="cafe-content">
            <div class="cafe-image">
                <img src="{{ asset('images/gerai.jpeg') }}" alt="Cafe Gerai 3 Abdul">                
            </div>
            <div class="cafe-info">
                <h2>Cafe Gerai 3 Abdul</h2>
                <p>Cafe Gerai 3 Abdul adalah mitra pertama kami yang berlokasi di Bengkalis. Sebagai kafe modern yang ramai dikunjungi, mereka memahami pentingnya efisiensi dalam pelayanan pelanggan.</p>
                <p>Dengan menggunakan sistem QRFood, Cafe Gerai 3 Abdul berhasil meningkatkan kecepatan pelayanan mereka hingga 50% dan memberikan pengalaman dining yang lebih modern kepada pelanggan mereka.</p>
                <div class="location-box">
                    <h3>📍 Lokasi</h3>
                    <p>Jalan Sriwijaya, Pambang Baru, Kecamatan Bantan, Kabupaten Bengkalis, Riau</p>
                    <p style="margin-top: 0.5rem; font-size: 0.9rem; color: #A8201A;">📞 Hubungi: 0812-xxxx-xxxx</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about">
        <div class="about-content">
            <h2>Tentang QRFood</h2>
            <p>QRFood adalah aplikasi pemesanan menu berbasis QR Barcode yang dirancang khusus untuk restoran, kafe, dan usaha kuliner di Bengkalis dan sekitarnya. Dengan teknologi modern, kami membantu meningkatkan efisiensi pelayanan dan pengalaman pelanggan Anda.</p>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number">50%</div>
                    <div class="stat-label">Pengurangan Waktu Antrian</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">90%</div>
                    <div class="stat-label">Akurasi Pesanan</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Kepuasan Pelanggan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="fitur">
        <div class="features-content">
            <h2>Fitur Unggulan</h2>
            <p class="features-subtitle">Solusi lengkap untuk kebutuhan digitalisasi restoran Anda</p>
            
            <div class="feature-grid">
                <div class="feature-card">
                    <div class="feature-icon">📱</div>
                    <h3>Scan QR Code</h3>
                    <p>Pelanggan scan QR di meja untuk langsung mengakses menu digital interaktif tanpa perlu menunggu pelayan</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🍽️</div>
                    <h3>Menu Digital</h3>
                    <p>Tampilan menu yang menarik dengan foto makanan, deskripsi lengkap, dan harga yang jelas</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">💳</div>
                    <h3>Pembayaran Digital</h3>
                    <p>Mendukung berbagai metode pembayaran: E-Wallet, QRIS, dan kartu debit/kredit untuk transaksi yang cepat</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">🖥️</div>
                    <h3>Dashboard Kasir</h3>
                    <p>Interface kasir yang user-friendly untuk mengelola pesanan masuk dengan mudah dan efisien</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">👨‍💼</div>
                    <h3>Panel Admin</h3>
                    <p>Kelola menu, stok, dan pantau penjualan real-time dengan dashboard admin yang komprehensif</p>
                </div>
                
                <div class="feature-card">
                    <div class="feature-icon">📊</div>
                    <h3>Laporan Penjualan</h3>
                    <p>Analisis mendalam terhadap performa bisnis untuk membantu pengambilan keputusan yang tepat</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Reviews Section -->
    <section class="reviews" id="ulasan">
        <div class="reviews-content">
            <h2>⭐ Ulasan Pelanggan</h2>
            <p style="text-align: center; color: #666; margin-bottom: 2rem;">Bagikan pengalaman Anda menggunakan QRFood</p>
            
            <!-- Form Input Ulasan -->
            <div class="review-form-container">
                <div class="review-form">
                    <h3>Tulis Ulasan Anda</h3>
                    <form id="reviewForm">
                        <div class="form-group">
                            <input type="text" id="reviewerName" placeholder="Nama Anda" required>
                        </div>
                        <div class="form-group">
                            <div class="star-rating">
                                <span class="star" data-rating="1">⭐</span>
                                <span class="star" data-rating="2">⭐</span>
                                <span class="star" data-rating="3">⭐</span>
                                <span class="star" data-rating="4">⭐</span>
                                <span class="star" data-rating="5">⭐</span>
                            </div>
                            <input type="hidden" id="rating" value="5" required>
                        </div>
                        <div class="form-group">
                            <textarea id="reviewText" placeholder="Ceritakan pengalaman Anda..." rows="4" required></textarea>
                        </div>
                        <button type="submit" class="submit-review-btn">Kirim Ulasan</button>
                    </form>
                </div>
            </div>
            
            <!-- Display Reviews -->
            <div class="reviews-grid" id="reviewsGrid">
                <!-- Reviews will be displayed here dynamically -->
            </div>
        </div>
    </section>

    <!-- Target Section -->
    <section class="target" id="target">
        <div class="target-content">
            <h2>Siapa Yang Kami Layani?</h2>
            
            <div class="target-grid">
                <div class="target-card">
                    <div class="target-icon">🏪</div>
                    <h3>Restoran & Kafe</h3>
                    <p>Tempat makan yang ingin meningkatkan kualitas pelayanan tanpa menambah banyak karyawan</p>
                </div>
                
                <div class="target-card">
                    <div class="target-icon">🍜</div>
                    <h3>Warung Makan</h3>
                    <p>Usaha kuliner yang ingin tampil lebih modern dan profesional di mata pelanggan</p>
                </div>
                
                <div class="target-card">
                    <div class="target-icon">☕</div>
                    <h3>Coffee Shop</h3>
                    <p>Tempat nongkrong anak muda yang mengutamakan efisiensi dan pengalaman digital</p>
                </div>
                
                <div class="target-card">
                    <div class="target-icon">🎯</div>
                    <h3>UMKM Kuliner</h3>
                    <p>Pelaku usaha di Bengkalis yang siap mengikuti perkembangan teknologi</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="team">
        <div class="team-content">
            <h2>Tim Pengembang</h2>
            
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar" style="overflow: hidden;">
                        <img src="{{ asset('images/Syah.jpg') }}" alt="Farhan Syahputra" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="team-name">Farhan Syahputra</div>
                    <div class="team-id">6304230040</div>
                    <div class="team-role">UI/UX Designer</div>
                </div>

                <div class="team-card">
                    <div class="team-avatar" style="overflow: hidden;">
                        <img src="{{ asset('images/Ihsan.jpeg') }}" alt="M. Ihsan Suryadin" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="team-name">M. Ihsan Suryadin</div>
                    <div class="team-id">6304230043</div>
                    <div class="team-role">Project Manager</div>
                </div>
                
                <div class="team-card">
                    <div class="team-avatar" style="overflow: hidden;">
                        <img src="{{ asset('images/Farhan.jpeg') }}" alt="Muhamad Farhan Sidiq" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="team-name">Muhamad Farhan Sidiq</div>
                    <div class="team-id">6304230041</div>
                    <div class="team-role">Developer</div>
                </div>
                
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <h2>Siap Meningkatkan Bisnis Anda?</h2>
        <p>Hubungi kami untuk demo gratis dan konsultasi sistem pemesanan digital</p>
        <button class="btn-primary" onclick="alert('Hubungi kami melalui kontak yang tersedia!')">Hubungi Kami</button>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>QRFood</h3>
                <p>Sistem Pemesanan Menu Berbasis QR Barcode untuk restoran dan kafe modern di Bengkalis.</p>
            </div>
            
            <div class="footer-section">
                <h3>Layanan</h3>
                <ul>
                    <li>Konsultasi Sistem</li>
                    <li>Implementasi</li>
                    <li>Pelatihan User</li>
                    <li>Maintenance & Support</li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>Kontak</h3>
                <p>Program Studi D4 Rekayasa Perangkat Lunak<br>
                Jurusan Teknik Informatika<br>
                Politeknik Negeri Bengkalis<br>
                2025</p>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2025 QRFood - Kelompok 8. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <div class="scroll-top" id="scrollTop" onclick="window.scrollTo({top: 0, behavior: 'smooth'})">
        ↑
    </div>

    <script>
        // Smooth scroll for navigation
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', function() {
            const header = document.getElementById('header');
            const scrollTop = document.getElementById('scrollTop');
            
            if (window.scrollY > 100) {
                header.classList.add('scrolled');
                scrollTop.classList.add('visible');
            } else {
                header.classList.remove('scrolled');
                scrollTop.classList.remove('visible');
            }
        });

        // Intersection Observer for animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe all cards
        document.querySelectorAll('.feature-card, .target-card, .team-card, .stat-card, .review-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'all 0.6s ease';
            observer.observe(el);
        });

        // Add parallax effect to hero section
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('.hero');
            if (hero) {
                hero.style.transform = 'translateY(' + scrolled * 0.5 + 'px)';
            }
        });

        // Review System
        let reviews = [];
        let currentRating = 5;

        // Load reviews from memory
        function loadReviews() {
            const reviewsGrid = document.getElementById('reviewsGrid');
            
            if (reviews.length === 0) {
                reviewsGrid.innerHTML = `
                    <div class="empty-reviews">
                        <div class="empty-reviews-icon">💬</div>
                        <p>Belum ada ulasan. Jadilah yang pertama memberikan ulasan!</p>
                    </div>
                `;
                return;
            }

            reviewsGrid.innerHTML = reviews.map(review => `
                <div class="review-card" style="opacity: 0; transform: translateY(20px);">
                    <div class="review-header">
                        <div class="review-avatar">${getInitials(review.name)}</div>
                        <div class="review-info">
                            <h4>${escapeHtml(review.name)}</h4>
                            <div class="review-stars">${'⭐'.repeat(review.rating)}</div>
                        </div>
                    </div>
                    <p class="review-text">${escapeHtml(review.text)}</p>
                    <p style="font-size: 0.85rem; color: #999; margin-top: 1rem;">${review.date}</p>
                </div>
            `).join('');

            // Animate new reviews
            setTimeout(() => {
                document.querySelectorAll('.review-card').forEach((card, index) => {
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                        card.style.transition = 'all 0.6s ease';
                    }, index * 100);
                });
            }, 100);
        }

        // Get initials from name
        function getInitials(name) {
            const words = name.trim().split(' ');
            if (words.length >= 2) {
                return (words[0][0] + words[1][0]).toUpperCase();
            }
            return name.substring(0, 2).toUpperCase();
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Star rating functionality
        const stars = document.querySelectorAll('.star');
        stars.forEach(star => {
            star.addEventListener('click', function() {
                currentRating = parseInt(this.getAttribute('data-rating'));
                document.getElementById('rating').value = currentRating;
                
                stars.forEach(s => {
                    const rating = parseInt(s.getAttribute('data-rating'));
                    if (rating <= currentRating) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
        });

        // Set default rating
        stars.forEach(s => s.classList.add('active'));

        // Form submission
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('reviewerName').value.trim();
            const text = document.getElementById('reviewText').value.trim();
            const rating = parseInt(document.getElementById('rating').value);

            if (!name || !text) {
                alert('Mohon isi semua field!');
                return;
            }

            // Add review
            const newReview = {
                name: name,
                rating: rating,
                text: text,
                date: new Date().toLocaleDateString('id-ID', { 
                    day: 'numeric', 
                    month: 'long', 
                    year: 'numeric' 
                })
            };

            reviews.unshift(newReview); // Add to beginning
            loadReviews();

            // Reset form
            this.reset();
            currentRating = 5;
            stars.forEach(s => s.classList.add('active'));

            // Show success message
            alert('Terima kasih atas ulasan Anda! 🎉');

            // Scroll to reviews
            document.getElementById('reviewsGrid').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'nearest' 
            });
        });

        // Initial load
        loadReviews();
    </script>
</body>
</html>
