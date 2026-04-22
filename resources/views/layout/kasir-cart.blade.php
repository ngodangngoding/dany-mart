<aside class="flex flex-col gap-4">
    <section class="dashboard-cart-card flex flex-col" data-cart-sidebar>
        <div data-cart-empty class="flex flex-1 items-center justify-center text-center">
            <div>
                <p class="text-base font-semibold text-slate-500">Item kosong</p>
            </div>
        </div>

        <div data-cart-content class="hidden flex-1 flex-col">
            <div>
                <h3 class="text-[15px] font-semibold text-slate-900">Item Dipilih</h3>
            </div>

            <div data-cart-items class="mt-4 flex-1 space-y-3 overflow-y-auto pr-1"></div>

            <div class="dashboard-cart-separator mt-5"></div>

            <div class="mt-5">
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-[14px] text-slate-500">
                        <span>Sub total</span>
                        <span data-cart-subtotal class="font-medium text-slate-700">Rp0</span>
                    </div>
                    <div class="flex items-end justify-between gap-3">
                        <span class="text-[14px] text-slate-500">Total pembayaran</span>
                        <span data-cart-total class="text-[22px] font-bold leading-none text-kasir-primary">Rp0</span>
                    </div>
                </div>
            </div>

            <div class="dashboard-cart-separator mt-5"></div>

            <div class="mt-5 dashboard-payment-panel">
                <p class="text-[14px] text-slate-500">Metode pembayaran:</p>
                <div class="mt-3 grid grid-cols-2 gap-3" data-payment-methods>
                    <button
                        type="button"
                        data-payment-method="Tunai"
                        class="dashboard-payment-method is-active"
                        aria-pressed="true"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <rect x="2.75" y="5.25" width="14.5" height="9.5" rx="2" stroke="currentColor" stroke-width="1.5" />
                            <circle cx="10" cy="10" r="2" stroke="currentColor" stroke-width="1.5" />
                            <path d="M5.25 8.5h.01M14.74 11.5h.01" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                        </svg>
                        <span>Tunai</span>
                    </button>
                    <button
                        type="button"
                        data-payment-method="QRIS"
                        class="dashboard-payment-method"
                        aria-pressed="false"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M3.5 6.5v-2h2M14.5 4.5h2v2M16.5 13.5v2h-2M5.5 15.5h-2v-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            <rect x="6.75" y="6.75" width="6.5" height="6.5" rx="1.5" stroke="currentColor" stroke-width="1.5" />
                        </svg>
                        <span>QRIS</span>
                    </button>
                </div>
            </div>

            <div class="dashboard-cart-separator mt-5"></div>

            <div class="mt-5 flex items-center justify-between gap-3">
                <h3 class="text-[15px] font-bold text-slate-900">Ringkasan Pembayaran</h3>
                <span class="dashboard-summary-badge" data-cart-summary-count>
                    0 Item
                </span>
            </div>

            <div class="mt-5 space-y-3">
                <button
                    type="button"
                    data-open-payment-modal
                    class="dashboard-pay-button"
                >
                    Bayar (F9)
                </button>

                <button type="button" data-clear-cart class="dashboard-clear-button">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M6.5 7.5v6M10 7.5v6M13.5 7.5v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        <path d="M3.5 5.5h13M7 3.5h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                        <path d="M5.5 5.5v9a1.5 1.5 0 0 0 1.5 1.5h6a1.5 1.5 0 0 0 1.5-1.5v-9" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                    </svg>
                    <span>Hapus keranjang</span>
                </button>
            </div>
        </div>
    </section>
</aside>
