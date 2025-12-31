<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Gerai 3 Abdul</title>
    
    <!-- CSS Internal -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #D4C0A0; /* Background krem */
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        .container {
            width: 100%;
            max-width: 480px; /* Lebar maks seperti di HP */
            margin: 0 auto;
            background-color: #F0E6D8; /* Background konten */
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            box-sizing: border-box; 
            padding-bottom: 100px; /* Ruang untuk footer */
        }
        .header {
            text-align: center;
            padding: 1rem;
            position: relative;
        }
        .header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
        }
        /* Tombol Menu (Garis 3) */
        .menu-btn {
            position: absolute;
            top: 15px;
            left: 15px;
            background: none;
            border: none;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 5px;
            z-index: 100;
            padding: 5px;
        }
        .menu-btn span {
            display: block;
            width: 30px;
            height: 4px;
            background-color: #333;
            border-radius: 4px;
        }
        
        /* Menu Logout (Dropdown) */
        .logout-menu {
            display: none; /* Sembunyi default */
            position: absolute;
            top: 55px; /* Muncul di bawah tombol menu */
            left: 15px;
            background-color: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            z-index: 99;
            min-width: 100px;
        }
        .logout-menu.show {
            display: block;
        }

        .btn-logout {
            background-color: #B91C1C;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            width: 100%;
        }
        .content {
            padding: 0 1.5rem;
        }
        .content-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #333;
            margin-bottom: 1rem;
            text-align: center;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 1rem;
        }
        .tab-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background-color: #C3B091;
            color: #333;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
        }
        .tab-btn.active {
            background-color: #B91C1C;
            color: white;
        }
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 1.5rem;
        }
        .search-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #BDBDBD;
            border-radius: 8px;
            font-size: 1rem;
            -webkit-text-size-adjust: 100%; 
        }
        .search-btn {
            padding: 10px 15px;
            border: none;
            border-radius: 8px;
            background-color: #B91C1C;
            color: white;
            font-size: 1rem;
            cursor: pointer;
        }
        .menu-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .menu-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            background-color: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 2px solid transparent;
            transition: border-color 0.3s;
        }
        .menu-item.search-highlight {
            border-color: #16A34A;
        }
        .menu-item img {
            width: 80px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }
        .menu-info {
            flex: 1;
            overflow: hidden; 
        }
        .menu-info h4 {
            margin: 0 0 5px 0;
            font-size: 1.1rem;
            color: #333;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .menu-info p {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            color: #B91C1C;
        }
        .menu-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .menu-controls p {
            margin: 0;
            font-weight: 600;
            display: none;
        }
        .quantity-btn {
            width: 30px;
            height: 30px;
            border: 1px solid #B91C1C;
            background-color: white;
            color: #B91C1C;
            border-radius: 50%;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0; 
        }
        .quantity-btn.plus {
            background-color: #B91C1C;
            color: white;
        }
        .quantity-display {
            font-size: 1.1rem;
            font-weight: bold;
            min-width: 20px;
            text-align: center;
        }
        .cart-footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            max-width: 480px; 
            margin: 0 auto;
            background-color: white;
            padding: 1rem 1.5rem;
            border-top: 1px solid #eee;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            box-sizing: border-box; 
        }
        .cart-summary {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-shrink: 1;
            overflow: hidden;
        }
        .cart-summary-icon {
            font-size: 1.5rem; 
            flex-shrink: 0;
        }
        .cart-total {
            font-size: 1rem;
            font-weight: 600;
            color: #555;
            white-space: nowrap; 
        }
        .btn-lanjut {
            background-color: #B91C1C;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            flex-shrink: 0; 
        }

        @media (max-width: 360px) {
            .content {
                padding: 0 1rem;
            }
            .menu-item {
                gap: 0.5rem;
                padding: 0.75rem;
            }
            .menu-info h4 {
                font-size: 1rem;
            }
            .menu-controls {
                gap: 5px;
            }
            .cart-summary {
                gap: 8px;
            }
            .cart-total {
                font-size: 0.9rem;
            }
            .btn-lanjut {
                padding: 8px 15px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <div class="container">
        <!-- Header: Logo dan Logout -->
        <div class="header">
            <!-- Tombol Menu (Hamburger) -->
            <button id="menu-btn" class="menu-btn">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <!-- Menu Logout (Hidden by default) -->
            <div id="logout-menu" class="logout-menu">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-logout">Logout</button>
                </form>
            </div>

            <img src="{{ asset('images/cafe.jpg') }}" alt="Logo Cafe 3 Abdul">
        </div>

        <div style="text-align:center; margin-top:-5px; margin-bottom: 12px;">
            <p style="
                font-size: 1.1rem;
                font-weight: 700;
                color: #4a3728;
                font-style: italic;
                margin-bottom: 4px;
            ">
                Hi, {{ Auth::user()->name }}. Mau pesan apa?
            </p>
        </div>


        <!-- Konten Utama: Tabs, Search, Menu -->
        <div class="content">
            
            <!-- Tabs Kategori -->
            <div class="tabs">
                <button class="tab-btn active" data-category="makanan">Makanan</button>
                <button class="tab-btn" data-category="minuman">Minuman</button>
                <button class="tab-btn" data-category="paket">Paket</button>
            </div>
            
            <!-- Search Bar -->
            <div class="search-bar">
                <input type="text" id="search-input" class="search-input" placeholder="Cari menu...">
                <button id="search-btn" class="search-btn">Cari</button>
            </div>
            
            <!-- Daftar Menu (akan diisi oleh JavaScript) -->
            <div id="menu-list-container" class="menu-list">
                <!-- Konten diisi oleh JS -->
            </div>
        </div>
    </div>

    <!-- Footer Keranjang (Sticky) -->
    <div class="cart-footer">
        <div class="cart-summary">
            <span class="cart-summary-icon">🛒</span> 
            <div class="cart-total">
                <span id="total-items">0 Item</span> | 
                <span id="total-price">Rp 0</span>
            </div>
        </div>
        <form action="{{ route('pesanan.hitung') }}" method="POST" id="cart-form">
            @csrf
            <input type="hidden" name="cart" id="cart-input">
            <button type="submit" class="btn-lanjut">Lanjut</button>
        </form>
    </div>

    <!-- JavaScript -->
    <script>
        // Ambil data produk dari Blade (Laravel) ke JavaScript
        const allProducts = @json($produks); // Ini HANYA menggunakan $produks
        
        let cart = {}; 
        let currentCategory = 'makanan';
        let currentSearchTerm = '';

        // Referensi ke elemen-elemen penting
        const menuListContainer = document.getElementById('menu-list-container');
        const tabButtons = document.querySelectorAll('.tab-btn');
        const searchInput = document.getElementById('search-input');
        const searchButton = document.getElementById('search-btn');
        const totalItemsEl = document.getElementById('total-items');
        const totalPriceEl = document.getElementById('total-price');
        const cartInput = document.getElementById('cart-input');

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(number);
        }

        function renderProducts() {
            menuListContainer.innerHTML = '';
            
            const filteredProducts = allProducts.filter(product => {
                const categoryMatch = product.category === currentCategory;
                const searchMatch = product.name.toLowerCase().includes(currentSearchTerm.toLowerCase());
                return categoryMatch && searchMatch;
            });

            if (filteredProducts.length === 0) {
                menuListContainer.innerHTML = '<p>Menu tidak ditemukan.</p>';
            } else {
                filteredProducts.forEach(product => {
                    const quantity = cart[product.id] || 0;
                    const item = document.createElement('div');
                    item.className = 'menu-item';
                    item.setAttribute('data-product-id', product.id);

                    if (currentSearchTerm && product.name.toLowerCase().includes(currentSearchTerm.toLowerCase())) {
                        item.classList.add('search-highlight');
                    }

                    item.innerHTML = `
                        <img src="${product.image_url}" alt="${product.name}">
                        <div class="menu-info">
                            <h4>${product.name}</h4>
                            <p>${formatRupiah(product.price)}</p>
                        </div>
                        <div class="menu-controls">
                            <p>Jumlah:</p>
                            <button class="quantity-btn minus" data-id="${product.id}">-</button>
                            <span class="quantity-display" data-id="${product.id}">${quantity}</span>
                            <button class="quantity-btn plus" data-id="${product.id}">+</button>
                        </div>
                    `;
                    menuListContainer.appendChild(item);
                });
            }
        }

        function updateFooter() {
            let totalItems = 0;
            let totalPrice = 0;

            for (const productId in cart) {
                const quantity = cart[productId];
                if (quantity > 0) {
                    const product = allProducts.find(p => p.id == productId);
                    if (product) {
                        totalItems += quantity;
                        totalPrice += product.price * quantity;
                    }
                }
            }

            totalItemsEl.textContent = `${totalItems} Item`;
            totalPriceEl.textContent = formatRupiah(totalPrice);
            
            if (cartInput) {
                cartInput.value = JSON.stringify(cart);
            }
        }

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');
                currentCategory = button.getAttribute('data-category');
                renderProducts();
            });
        });

        searchButton.addEventListener('click', () => {
            currentSearchTerm = searchInput.value;
            renderProducts();
        });
        
        searchInput.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') {
                searchButton.click();
            }
        });

        menuListContainer.addEventListener('click', (e) => {
            const target = e.target;
            let productId = null;
            let change = 0;

            if (target.classList.contains('plus')) {
                productId = target.getAttribute('data-id');
                change = 1;
            } else if (target.classList.contains('minus')) {
                productId = target.getAttribute('data-id');
                change = -1;
            }

            if (productId) {
                let currentQuantity = cart[productId] || 0;
                let newQuantity = currentQuantity + change;

                if (newQuantity < 0) {
                    newQuantity = 0;
                }
                
                if (newQuantity > 0) {
                    cart[productId] = newQuantity;
                } else {
                    delete cart[productId];
                }

                document.querySelector(`.quantity-display[data-id="${productId}"]`).textContent = newQuantity;
                
                updateFooter();
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            renderProducts();
            updateFooter(); 
            
            // Logika Toggle Menu Logout
            const menuBtn = document.getElementById('menu-btn');
            const logoutMenu = document.getElementById('logout-menu');

            menuBtn.addEventListener('click', (e) => {
                e.stopPropagation(); // Mencegah event klik tembus ke document
                logoutMenu.classList.toggle('show');
            });

            // Tutup menu jika klik di luar area menu
            document.addEventListener('click', (e) => {
                if (!menuBtn.contains(e.target) && !logoutMenu.contains(e.target)) {
                    logoutMenu.classList.remove('show');
                }
            });
        });

    </script>
</body>
</html>