@php
    $category = request('category', 'semua');
    $products = [
        ['kode' => 'BRG-001', 'nama' => 'Air Mineral 600ml', 'kat' => 'minuman', 'jual' => 5000, 'stok' => 120],
        ['kode' => 'BRG-002', 'nama' => 'Es Teh Jumbo', 'kat' => 'minuman', 'jual' => 8000, 'stok' => 81],
        ['kode' => 'BRG-003', 'nama' => 'Chiki Balls', 'kat' => 'snack', 'jual' => 3500, 'stok' => 50],
        ['kode' => 'BRG-004', 'nama' => 'Roti Bakar Madu', 'kat' => 'makanan', 'jual' => 15000, 'stok' => 32],
        ['kode' => 'BRG-005', 'nama' => 'Buku Tulis Sidu', 'kat' => 'alat tulis', 'jual' => 5000, 'stok' => 100],
        ['kode' => 'BRG-006', 'nama' => 'Panadol Extra', 'kat' => 'medicine', 'jual' => 12500, 'stok' => 25],
        ['kode' => 'BRG-007', 'nama' => 'Beras 5kg', 'kat' => 'sembako', 'jual' => 75000, 'stok' => 15],
        ['kode' => 'BRG-008', 'nama' => 'Lifebuoy 80gr', 'kat' => 'alat mandi', 'jual' => 4500, 'stok' => 40],
        ['kode' => 'BRG-009', 'nama' => 'Sampoerna Mild', 'kat' => 'rokok', 'jual' => 35000, 'stok' => 20],
    ];
    $categories = ['semua', 'minuman', 'snack', 'makanan', 'alat tulis', 'medicine', 'sembako', 'alat mandi', 'rokok'];
@endphp

<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>POS System - Admin</title>
        <link rel="stylesheet" href="/css/admin.css">
    </head>
    <body>
        <div class="app-container">
            @include('layout.admin-sidebar')

            <main class="main-content">
                @include('layout.admin-topbar', ['title' => 'Admin', 'subtitle' => 'Toko Altap', 'user' => 'Dimsum (Admin)'])

                <div class="search-section">
                    <input type="text" placeholder="Cari produk (F2)...">
                </div>

                <div class="filter-bar">
                    @foreach ($categories as $item)
                        <a href="{{ route('admin.dashboard', ['category' => $item]) }}" class="{{ $category === $item ? 'btn-active' : '' }}">
                            {{ ucfirst($item) }}
                        </a>
                    @endforeach
                </div>

                <div class="product-grid">
                    @foreach ($products as $product)
                        @if ($category === 'semua' || $product['kat'] === $category)
                            <div class="card">
                                <strong>{{ $product['nama'] }}</strong>
                                <br>
                                <small>{{ $product['kode'] }}</small>
                                <p class="price">Rp {{ number_format($product['jual'], 0, ',', '.') }}</p>
                                <span class="stock">Stok: {{ $product['stok'] }}</span>
                                <button class="add-btn" type="button">+</button>
                            </div>
                        @endif
                    @endforeach
                </div>
            </main>

            <aside class="cart-panel">
                <div class="cart-title">Pesanan Baru</div>
                <div class="cart-empty">Item kosong</div>
            </aside>
        </div>
    </body>
</html>
