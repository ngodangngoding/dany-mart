<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Dashboard Kasir</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="dashboard-shell">
        @php
            $defaultCategories = ['Semua produk', 'Snack', 'Minuman', 'Alat Tulis Kantor', 'Medicine', 'Lainnya'];

            $defaultProducts = [
                ['id' => 1, 'name' => 'Piatos', 'BRG' => 'BRG-0S1', 'category' => 'Snack', 'price' => 12000, 'stock' => 120, 'sold_count' => 50],
                ['id' => 2, 'name' => 'Pulpen', 'BRG' => 'BRG-0S2', 'category' => 'Alat Tulis Kantor', 'price' => 18000, 'stock' => 33, 'sold_count' => 15],
                ['id' => 3, 'name' => 'Paracetamol', 'BRG' => 'BRG-0B3', 'category' => 'Medicine', 'price' => 15000, 'stock' => 32, 'sold_count' => 80],
                ['id' => 4, 'name' => 'Le Mineral', 'BRG' => 'BRG-0A1', 'category' => 'Minuman', 'price' => 15000, 'stock' => 3, 'sold_count' => 99],
                ['id' => 5, 'name' => 'Tehpucuk', 'BRG' => 'BRG-001', 'category' => 'Minuman', 'price' => 5000, 'stock' => 120, 'sold_count' => 85],
                ['id' => 6, 'name' => 'Roma kelapa', 'BRG' => 'BRG-002', 'category' => 'Snack', 'price' => 8000, 'stock' => 81, 'sold_count' => 34],
                ['id' => 7, 'name' => 'kecap', 'BRG' => 'BRG-003', 'category' => 'Lainnya', 'price' => 8000, 'stock' => 32, 'sold_count' => 25],
            ];

            $categories = $categories ?? $defaultCategories;
            $rawProducts = $products ?? $defaultProducts;

            $products = collect($rawProducts)->map(function ($product) {
                if (is_object($product)) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'BRG' => $product->BRG ?? $product->sku ?? '-',
                        'sku' => $product->sku ?? $product->BRG ?? '-',
                        'category' => $product->category ?? 'Lainnya',
                        'price' => (int) $product->price,
                        'stock' => (int) $product->stock,
                        'sold_count' => (int) ($product->sold_count ?? 0),
                    ];
                }

                return [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'BRG' => $product['BRG'] ?? ($product['sku'] ?? '-'),
                    'sku' => $product['sku'] ?? ($product['BRG'] ?? '-'),
                    'category' => $product['category'] ?? 'Lainnya',
                    'price' => (int) $product['price'],
                    'stock' => (int) $product['stock'],
                    'sold_count' => (int) ($product['sold_count'] ?? 0),
                ];
            })->values();
        @endphp

        <script>
            window.posProducts = @json($products);
            window.posConfig = {
                checkoutUrl: "{{ route('kasir.checkout') }}",
                orderBaseUrl: "{{ url('/kasir/orders') }}",
                csrfToken: "{{ csrf_token() }}"
            };
        </script>

        <div
            id="cart-toast"
            class="pointer-events-none fixed left-1/2 top-6 z-50 hidden w-[calc(100%-2rem)] max-w-sm -translate-x-1/2 sm:top-8"
            aria-live="polite"
        ></div>

        <div class="min-h-screen xl:grid xl:grid-cols-[280px_minmax(0,1fr)_320px]">
            @include('layout.kasir-sidebar')

            <main class="min-w-0 px-4 py-5 sm:px-6 lg:px-8 lg:py-8">
                <div class="mx-auto flex max-w-7xl flex-col gap-6">
                    @include('layout.kasir-topbar')

                    <section class="dashboard-panel p-5 sm:p-6">
                        <div class="flex flex-col gap-5">
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-4 inline-flex items-center text-slate-400">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                        <circle cx="9" cy="9" r="5.75" stroke="currentColor" stroke-width="1.5" />
                                        <path d="M13.5 13.5 17 17" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                    </svg>
                                </span>
                                <input
                                    type="search"
                                    data-product-search
                                    placeholder="Cari produk (F2)"
                                    class="w-full rounded-2xl border border-slate-200 bg-white py-3 pl-12 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-kasir-primary focus:ring-2 focus:ring-kasir-pastel"
                                >
                            </div>

                            <div class="flex flex-wrap gap-3" data-category-tabs>
                                @foreach ($categories as $index => $category)
                                    <button
                                        type="button"
                                        data-category-tab
                                        data-category-value="{{ $category }}"
                                        aria-pressed="{{ $index === 0 ? 'true' : 'false' }}"
                                        class="dashboard-category-tab {{ $index === 0 ? 'is-active' : '' }}"
                                    >
                                        {{ $category }}
                                    </button>
                                @endforeach
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2 2xl:grid-cols-3" id="product-grid">
                                @foreach ($products as $product)
                                    <article
                                        class="dashboard-product-card"
                                        data-product-card
                                        data-product-name="{{ strtolower($product['name']) }}"
                                        data-product-category="{{ strtolower($product['category']) }}"
                                    >
                                        <div class="pr-14">
                                            <p class="text-lg font-bold text-slate-900">{{ $product['name'] }}</p>
                                            <p class="mt-1 text-sm text-slate-400">{{ $product['BRG'] }}</p>
                                            <p class="mt-5 text-xl font-bold text-kasir-primary">Rp {{ number_format($product['price'], 0, ',', '.') }}</p>
                                            <p class="mt-1 text-sm {{ $product['stock'] <= 5 ? 'text-red-500 font-medium' : 'text-slate-500' }}">
                                                Stok: {{ $product['stock'] }}
                                            </p>
                                        </div>

                                        <button
                                            type="button"
                                            data-cart-add
                                            data-id="{{ $product['id'] }}"
                                            data-name="{{ $product['name'] }}"
                                            data-BRG="{{ $product['BRG'] }}"
                                            data-sku="{{ $product['sku'] }}"
                                            data-category="{{ $product['category'] }}"
                                            data-price="{{ $product['price'] }}"
                                            data-stock="{{ $product['stock'] }}"
                                            data-sold_count="{{ $product['sold_count'] }}"
                                            class="dashboard-add-button absolute bottom-5 right-5"
                                            aria-label="Tambah {{ $product['name'] }}"
                                        >
                                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                                <path d="M10 4.5v11M4.5 10h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                            </svg>
                                        </button>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </section>
                </div>
            </main>

            <div class="dashboard-cart-column px-4 pb-5 sm:px-6 lg:px-8 lg:pb-8 xl:px-6 xl:pb-8">
                <!-- Spacer khusus untuk menyejajarkan keranjang dengan panel produk di layar besar -->
                <style>
                    .cart-spacer { display: none; }
                    @media (min-width: 1280px) {
                        .cart-spacer { display: block; height: 150px; }
                    }
                </style>
                <div class="cart-spacer" aria-hidden="true"></div>
                
                @include('layout.kasir-cart')
            </div>
        </div>

        <div id="recommendation-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center dashboard-modal-backdrop px-4">
            <div class="dashboard-modal-card w-full max-w-md flex flex-col relative overflow-hidden animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-slate-100 p-5">
                    <h2 class="text-[17px] font-bold text-slate-900">Rekomendasi produk</h2>
                    <button type="button" data-close-recommendation class="dashboard-collapse-button h-8 w-8 !border-none bg-slate-50 hover:bg-slate-100">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <div class="p-5 flex flex-col gap-4 bg-slate-50/50" id="recommendation-list">
                    <!-- Dinamis via JS -->
                </div>

                <div class="border-t border-slate-100 bg-white p-5">
                    <p class="text-center text-sm font-semibold text-slate-700 mb-4">Masukkan ke keranjang</p>
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" id="btn-rekomendasi-iya" class="dashboard-clear-button !border-kasir-primary !text-kasir-primary hover:!bg-kasir-pastel hover:!text-kasir-primary-dark">
                            Iya / Tambahkan
                        </button>
                        <button type="button" id="btn-rekomendasi-tidak" class="dashboard-clear-button !border-slate-200 !text-slate-600 hover:!bg-slate-50 hover:!text-slate-900">
                            Tidak
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div
            id="payment-modal"
            data-payment-modal
            class="hidden fixed inset-0 z-[110] flex items-center justify-center bg-slate-900/20 px-4"
        >
            <div class="w-full max-w-[720px] rounded-[18px] bg-[#f3eded] p-4 shadow-2xl sm:p-5">
                <div class="mb-5 flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-[30px] font-bold leading-none text-slate-900">Pembayaran</h3>
                        <p class="mt-4 text-sm text-slate-700">
                            Silakan pilih metode pembayaran dan masukkan jumlah yang dibayarkan
                        </p>
                    </div>

                    <button
                        type="button"
                        data-close-payment-modal
                        class="inline-flex h-10 w-10 items-center justify-center rounded-full text-slate-700 hover:bg-slate-200"
                        aria-label="Tutup popup pembayaran"
                    >
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <div class="mb-5 rounded-2xl bg-[#e7eef8] px-4 py-4">
                    <p class="mb-2 text-sm text-slate-600">Total yang harus dibayar:</p>
                    <p data-payment-total class="text-right text-[38px] font-bold leading-none text-kasir-primary">
                        Rp 0
                    </p>
                </div>

                <div class="mb-4">
                    <p class="mb-3 text-sm font-medium text-slate-700">Metode pembayaran</p>

                    <div class="grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            data-payment-method="Tunai"
                            class="dashboard-payment-option is-active flex items-center justify-center gap-2 rounded-2xl border border-kasir-primary bg-white px-4 py-4 text-sm font-semibold text-kasir-primary"
                            aria-pressed="true"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <rect x="3.5" y="5.5" width="13" height="9" rx="1.5" stroke="currentColor" stroke-width="1.5" />
                                <circle cx="10" cy="10" r="1.5" fill="currentColor" />
                            </svg>
                            Tunai
                        </button>

                        <button
                            type="button"
                            data-payment-method="QRIS"
                            class="dashboard-payment-option flex items-center justify-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm font-semibold text-slate-700"
                            aria-pressed="false"
                        >
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M4.5 7.5v-2a1 1 0 0 1 1-1h2M15.5 7.5v-2a1 1 0 0 0-1-1h-2M4.5 12.5v2a1 1 0 0 0 1 1h2M15.5 12.5v2a1 1 0 0 1-1 1h-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M7.5 7.5h1.5v1.5H7.5zM11 7.5h1.5V9H11zM7.5 11h1.5v1.5H7.5zM11 11h1.5v1.5H11z" fill="currentColor" />
                            </svg>
                            QRIS
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <p class="mb-2 text-sm font-medium text-slate-700">Jumlah bayar</p>
                    <input
                        type="number"
                        min="0"
                        step="1000"
                        data-payment-amount-input
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-base text-slate-900 outline-none focus:border-kasir-primary"
                        placeholder="Masukkan jumlah bayar"
                    >
                </div>

                <div class="mb-5 grid grid-cols-2 gap-2 sm:grid-cols-4">
                    <button type="button" data-payment-quick-amount="50000" class="rounded-xl bg-white px-3 py-3 text-sm text-slate-700 shadow-sm hover:bg-slate-50">Rp 50.000</button>
                    <button type="button" data-payment-quick-amount="100000" class="rounded-xl bg-white px-3 py-3 text-sm text-slate-700 shadow-sm hover:bg-slate-50">Rp 100.000</button>
                    <button type="button" data-payment-quick-amount="150000" class="rounded-xl bg-white px-3 py-3 text-sm text-slate-700 shadow-sm hover:bg-slate-50">Rp 150.000</button>
                    <button type="button" data-payment-quick-amount="200000" class="rounded-xl bg-white px-3 py-3 text-sm text-slate-700 shadow-sm hover:bg-slate-50">Rp 200.000</button>
                </div>

                <div class="mb-5 flex items-center justify-between text-sm text-slate-700">
                    <span>Kembalian</span>
                    <span data-payment-change class="font-semibold text-slate-900">Rp 0</span>
                </div>

                <button
                    type="button"
                    data-initiate-payment
                    class="w-full rounded-2xl bg-kasir-primary px-4 py-4 text-sm font-semibold text-white hover:opacity-90"
                >
                    Bayar
                </button>
            </div>
        </div>

        <div id="payment-confirmation-modal" class="hidden fixed inset-0 z-[120] flex items-center justify-center bg-slate-900/40 px-4 backdrop-blur-sm">
            <div class="dashboard-modal-card w-full max-w-sm flex flex-col relative overflow-hidden animate-in fade-in zoom-in duration-200 bg-white rounded-2xl p-6 shadow-2xl">
                <div class="flex items-center justify-center mb-5">
                    <div class="h-16 w-16 rounded-full bg-kasir-pastel flex items-center justify-center text-kasir-primary">
                        <svg class="h-8 w-8" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                
                <h3 class="text-xl font-bold text-slate-900 text-center mb-2">Konfirmasi Pembayaran</h3>
                <p class="text-sm text-slate-500 text-center mb-6">
                    Apakah Anda yakin ingin melanjutkan pembayaran ini? Pastikan nominal yang dimasukkan sudah benar.
                </p>

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" data-close-confirmation class="dashboard-clear-button !border-slate-200 !text-slate-600 hover:!bg-slate-50 hover:!text-slate-900 !rounded-xl !py-3">
                        Batalkan
                    </button>
                    <button type="button" data-confirm-checkout class="dashboard-clear-button !border-kasir-primary !bg-kasir-primary !text-white hover:opacity-90 !rounded-xl !py-3">
                        Lanjutkan
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Modal -->
        <div id="success-modal" class="hidden fixed inset-0 z-[130] flex items-center justify-center bg-white/95 backdrop-blur-sm px-4">
            <div class="w-full max-w-sm flex flex-col relative animate-in fade-in zoom-in duration-200">
                
                <div class="flex items-center justify-center gap-2 mb-6">
                    <div class="flex h-5 w-5 items-center justify-center rounded-full bg-[#49B660] text-white">
                        <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    </div>
                    <span class="font-bold text-[#49B660] text-lg">Transaksi Berhasil</span>
                </div>

                <!-- Green Card -->
                <div class="bg-gradient-to-b from-[#e3f1df] to-[#7eb25e] rounded-[30px] p-6 shadow-xl w-full mb-6 relative border border-[#c3e3ba]">
                    <div class="flex justify-center mb-4">
                        <div class="flex h-[60px] w-[60px] items-center justify-center rounded-full bg-[#3eb156] text-white shadow-lg border-[5px] border-[#d8ecd2]">
                            <svg class="h-8 w-8" viewBox="0 0 20 20" stroke="currentColor" stroke-width="1.5" fill="none"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    </div>
                    
                    <p class="text-center text-[#588142] text-[13px] font-medium mb-6 mt-2">Terimakasih Atas Pesanan Anda</p>

                    <!-- Receipt Items Box -->
                    <div class="bg-[#f2f9f2] rounded-2xl p-4 mb-4 shadow-sm border border-[#dcefda] space-y-3" id="success-receipt-items">
                        <!-- Dynamic items -->
                    </div>

                    <!-- Total Box -->
                    <div class="bg-[#dce9db]/80 rounded-2xl p-4 shadow-sm">
                        <div class="flex justify-between items-center mb-1.5">
                            <span class="font-semibold text-slate-600 text-[13px]">Total Tagihan</span>
                            <span class="font-bold text-slate-800 text-[13px]" id="success-total-tagihan">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center mb-1.5" id="success-paid-container">
                            <span class="font-semibold text-slate-600 text-[13px]">Tunai</span>
                            <span class="font-bold text-slate-800 text-[13px]" id="success-tunai">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-[#c3e3ba]/50 pb-2 mb-2" id="success-change-container">
                            <span class="font-semibold text-slate-600 text-[13px]">Kembalian</span>
                            <span class="font-bold text-[#588142] text-[13px]" id="success-kembalian">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center text-[13px] font-bold">
                            <span class="text-slate-700" id="success-payment-method">Metode: Tunai</span>
                            <span class="text-[#588142]">Lunas</span>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="space-y-4">
                    <button type="button" id="btn-share-struk" class="w-full rounded-2xl bg-[#8abc69] px-4 py-3 text-[15px] font-bold text-white hover:bg-[#72a353] transition shadow-sm flex justify-center items-center">
                        Share Struck
                    </button>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <button type="button" id="btn-pesanan-baru" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-[10px] text-[13px] font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm flex justify-center items-center gap-2">
                            <div class="bg-slate-900 text-white rounded-full p-[2px]">
                                <svg class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                            </div>
                            Pesanan Baru
                        </button>
                        <button type="button" id="btn-riwayat" class="w-full rounded-xl border border-orange-200 bg-white px-3 py-[10px] text-[13px] font-bold text-slate-700 hover:bg-orange-50 transition shadow-sm flex justify-center items-center gap-2">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Riwayat
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>