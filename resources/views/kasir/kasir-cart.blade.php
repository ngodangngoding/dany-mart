<div class="dashboard-panel p-5 sm:p-6" data-cart-sidebar>
    <div class="mb-4">
        <h3 class="text-lg font-bold text-slate-900">Keranjang</h3>
        <p id="current-order-number" class="mt-1 text-sm text-slate-500">Belum ada order</p>
    </div>

    <div data-cart-empty>
        <div class="rounded-2xl border border-dashed border-slate-200 p-4 text-sm text-slate-400">
            Belum ada item di keranjang.
        </div>
    </div>

    <div data-cart-content class="hidden">
        <div id="cart-items" data-cart-items class="space-y-3"></div>
    </div>

    <div class="mt-5 space-y-2 border-t border-slate-100 pt-4">
        <div class="flex items-center justify-between text-sm text-slate-500">
            <span>Subtotal</span>
            <span id="cart-subtotal" data-cart-subtotal>Rp0</span>
        </div>
        <div class="flex items-center justify-between text-base font-bold text-slate-900">
            <span>Total</span>
            <span id="cart-total" data-cart-total>Rp0</span>
        </div>
    </div>

    <div class="mt-5">
        <button
            type="button"
            id="btn-pay"
            data-open-payment-modal
            disabled
            class="w-full rounded-2xl bg-kasir-primary px-4 py-3 text-sm font-semibold text-white opacity-60 cursor-not-allowed hover:opacity-90"
        >
            Bayar
        </button>
    </div>
</div>