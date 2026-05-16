<section class="cart-card" data-cart-sidebar>
    <div class="cart-empty" data-cart-empty>
        <p>Item kosong</p>
    </div>

    <div class="cart-content hidden" data-cart-content>
        <h3>Item Dipilih</h3>
        <div class="cart-list" data-cart-items></div>
        <div class="checkout-actions">
            <div class="separator"></div>
            <div class="summary-row">
                <span class="muted">Sub total</span>
                <strong data-cart-subtotal>Rp0</strong>
            </div>
            <div class="summary-row">
                <span class="muted">Total pembayaran</span>
                <strong class="price" data-cart-total>Rp0</strong>
            </div>

            <div class="separator"></div>
            <div class="payment-panel">
                <p class="muted">Metode pembayaran:</p>
                <div class="payment-methods">
                    <button type="button" class="payment-method active" data-payment-method="Tunai" aria-pressed="true">Tunai</button>
                    <button type="button" class="payment-method" data-payment-method="QRIS" aria-pressed="false">QRIS</button>
                </div>
            </div>

            <div class="separator"></div>
            <div class="summary-row">
                <h3>Ringkasan Pembayaran</h3>
                <span class="summary-badge" data-cart-count>0 Item</span>
            </div>
            <button type="button" class="btn-pay" data-open-payment disabled>Bayar (F9)</button>
            <button type="button" class="btn-danger" data-clear-cart>Hapus keranjang</button>
        </div>
    </div>
</section>
