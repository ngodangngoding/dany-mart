<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Dashboard Kasir</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">
        <link rel="stylesheet" href="/css/admin.css">
        <link rel="stylesheet" href="/css/kasir-static.css">
        <script src="/js/kasir-static.js" defer></script>
    </head>
    <body class="dashboard-shell">
        <div class="toast hidden" data-toast aria-live="polite"></div>

        <div class="app-container kasir-dashboard-container">
            @include('layout.kasir-sidebar')

            <main class="main-content">
                @include('layout.kasir-topbar')

                <div class="search-section">
                    <input type="search" data-product-search placeholder="Cari produk (F2)...">
                </div>

                <div class="filter-bar" data-category-tabs></div>
                <div class="product-grid" data-product-grid></div>
            </main>

            <aside class="cart-panel">
                @include('layout.kasir-cart')
            </aside>
        </div>

        <div class="modal hidden" data-recommendation-modal>
            <section class="modal-card small">
                <header class="modal-header">
                    <h2 class="modal-title">Rekomendasi produk</h2>
                    <button type="button" class="icon-button" data-close-recommendation aria-label="Tutup rekomendasi">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                        </svg>
                    </button>
                </header>
                <div class="modal-body recommendation-list" data-recommendation-list></div>
                <footer class="modal-footer">
                    <p class="muted">Masukkan ke keranjang</p>
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" data-accept-recommendation>Iya / Tambahkan</button>
                        <button type="button" class="btn-secondary" data-skip-recommendation>Tidak</button>
                    </div>
                </footer>
            </section>
        </div>

        <div class="modal hidden" data-payment-modal>
            <section class="modal-card">
                <header class="modal-header">
                    <div>
                        <h2 class="modal-title">Pembayaran</h2>
                        <p class="muted">Pilih metode pembayaran dan masukkan jumlah yang dibayarkan.</p>
                    </div>
                    <button type="button" class="icon-button" data-close-payment aria-label="Tutup pembayaran">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M6 6l8 8M14 6l-8 8" stroke="currentColor" stroke-width="2" stroke-linecap="round"></path>
                        </svg>
                    </button>
                </header>
                <div class="modal-body form-stack">
                    <div class="payment-total-box">
                        <p class="muted">Total yang harus dibayar:</p>
                        <p class="payment-total" data-payment-total>Rp 0</p>
                    </div>
                    <div class="field">
                        <label>Metode pembayaran</label>
                        <div class="payment-grid">
                            <button type="button" class="payment-option active" data-payment-method="Tunai" aria-pressed="true">Tunai</button>
                            <button type="button" class="payment-option" data-payment-method="QRIS" aria-pressed="false">QRIS</button>
                        </div>
                    </div>
                    <div class="field">
                        <label for="payment-amount">Jumlah bayar</label>
                        <input id="payment-amount" type="number" min="0" step="1000" class="input" data-payment-amount placeholder="Masukkan jumlah bayar">
                    </div>
                    <div class="quick-amounts">
                        <button type="button" data-quick-amount="50000">Rp 50.000</button>
                        <button type="button" data-quick-amount="100000">Rp 100.000</button>
                        <button type="button" data-quick-amount="150000">Rp 150.000</button>
                        <button type="button" data-quick-amount="200000">Rp 200.000</button>
                    </div>
                    <div class="summary-row">
                        <span>Kembalian</span>
                        <strong data-payment-change>Rp 0</strong>
                    </div>
                    <button type="button" class="btn-pay" data-initiate-payment>Bayar</button>
                </div>
            </section>
        </div>

        <div class="modal hidden" data-confirmation-modal>
            <section class="modal-card small">
                <div class="modal-body form-stack">
                    <h2 class="modal-title">Konfirmasi Pembayaran</h2>
                    <p class="muted">Pastikan nominal pembayaran sudah benar sebelum melanjutkan.</p>
                    <div class="modal-actions">
                        <button type="button" class="btn-secondary" data-close-confirmation>Batalkan</button>
                        <button type="button" class="btn-pay" data-confirm-payment>Lanjutkan</button>
                    </div>
                </div>
            </section>
        </div>

        <div class="modal success-modal hidden" data-success-modal>
            <section class="success-panel">
                <div class="success-heading">
                    <span>Transaksi Berhasil</span>
                </div>
                <div class="success-receipt">
                    <div class="success-check">
                        <svg viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M5 10.5 8.2 13.7 15 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    <p class="receipt-note">Terimakasih Atas Pesanan Anda</p>
                    <div class="receipt-items" data-success-items></div>
                    <div class="receipt-summary">
                        <div class="receipt-line">
                            <span>Total Tagihan</span>
                            <span data-success-total>Rp 0</span>
                        </div>
                        <div class="receipt-line" data-success-paid-row>
                            <span>Tunai</span>
                            <span data-success-paid>Rp 0</span>
                        </div>
                        <div class="receipt-line" data-success-change-row>
                            <span>Kembalian</span>
                            <span data-success-change>Rp 0</span>
                        </div>
                        <div class="receipt-line">
                            <span data-success-method>Metode: Tunai</span>
                            <span>Lunas</span>
                        </div>
                    </div>
                </div>
                <div class="success-actions">
                    <button type="button" class="btn-share" data-share-receipt>Share Struk</button>
                    <div class="success-buttons-grid">
                        <button type="button" class="btn-secondary" data-new-order>Pesanan Baru</button>
                        <button type="button" class="btn-secondary" data-history-link>Riwayat</button>
                    </div>
                </div>
            </section>
        </div>
    </body>
</html>
