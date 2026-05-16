import './bootstrap';

let cart = [];
let selectedPaymentMethod = 'Tunai';
let toastTimeoutId = null;
let currentRecommendations = [];
let selectedRecommendationIds = new Set();
let activeOrder = null;
let isProcessingCheckout = false;
let hasReviewedRecommendations = false;

const formatRupiah = (amount) => new Intl.NumberFormat('id-ID').format(amount);

document.addEventListener('DOMContentLoaded', () => {
    const passwordToggles = document.querySelectorAll('[data-password-toggle]');

    passwordToggles.forEach((toggle) => {
        const targetId = toggle.getAttribute('data-target');
        const input = targetId ? document.getElementById(targetId) : null;

        if (!input) return;

        const label = toggle.querySelector('[data-password-label]');

        toggle.addEventListener('click', () => {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            toggle.setAttribute('aria-pressed', String(isHidden));

            if (label) {
                label.textContent = isHidden ? 'Hide' : 'Show';
            }
        });
    });

    const getCsrfToken = () =>
        window.posConfig?.csrfToken ||
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
        '';

    const normalizeProduct = (rawProduct) => ({
        id: Number(rawProduct.id),
        name: rawProduct.name ?? 'Produk',
        sku: rawProduct.sku ?? rawProduct.BRG ?? '-',
        BRG: rawProduct.BRG ?? rawProduct.sku ?? '-',
        category: rawProduct.category ?? 'Lainnya',
        price: Number(rawProduct.price ?? 0),
        stock: Number(rawProduct.stock ?? 0),
        sold_count: Number(rawProduct.sold_count ?? 0),
    });

    const allProducts = (window.posProducts || []).map(normalizeProduct);
    const productLookup = new Map(allProducts.map((product) => [product.id, product]));

    const categoryGroups = document.querySelectorAll('[data-category-tabs]');
    const productSearchInput = document.querySelector('[data-product-search]');
    const productCards = document.querySelectorAll('[data-product-card]');
    let activeCategory = 'semua produk';

    const cartSidebar = document.querySelector('[data-cart-sidebar]');
    const cartEmpty = cartSidebar?.querySelector('[data-cart-empty]');
    const cartContent = cartSidebar?.querySelector('[data-cart-content]');
    const cartItems = cartSidebar?.querySelector('[data-cart-items]');
    const cartSubtotal = cartSidebar?.querySelector('[data-cart-subtotal]');
    const cartTotal = cartSidebar?.querySelector('[data-cart-total]');
    const cartSummaryCount = cartSidebar?.querySelector('[data-cart-summary-count]');
    const payButton = cartSidebar?.querySelector('[data-open-payment-modal]');
    const clearCartButton = cartSidebar?.querySelector('[data-clear-cart]');
    const addButtons = document.querySelectorAll('[data-cart-add]');
    const toastContainer = document.getElementById('cart-toast');
    const currentOrderNumber = document.getElementById('current-order-number');

    const recommendationModal = document.getElementById('recommendation-modal');
    const recommendationList = document.getElementById('recommendation-list');

    const paymentModal = document.querySelector('[data-payment-modal]');
    const paymentButtons = document.querySelectorAll('[data-payment-method]');
    const paymentAmountInput = document.querySelector('[data-payment-amount-input]');
    const paymentQuickAmountButtons = document.querySelectorAll('[data-payment-quick-amount]');
    const paymentTotalTargets = document.querySelectorAll('[data-payment-total]');
    const paymentChangeTargets = document.querySelectorAll('[data-payment-change]');
    const initiatePaymentButton = document.querySelector('[data-initiate-payment]');
    const closePaymentButtons = document.querySelectorAll('[data-close-payment-modal]');

    const paymentConfirmationModal = document.getElementById('payment-confirmation-modal');
    const closeConfirmationButton = document.querySelector('[data-close-confirmation]');
    const confirmCheckoutButton = document.querySelector('[data-confirm-checkout]');

    const successModal = document.getElementById('success-modal');
    const successReceiptItems = document.getElementById('success-receipt-items');
    const successTotalTagihan = document.getElementById('success-total-tagihan');
    const successTunai = document.getElementById('success-tunai');
    const successKembalian = document.getElementById('success-kembalian');
    const successPaidContainer = document.getElementById('success-paid-container');
    const successChangeContainer = document.getElementById('success-change-container');
    const successPaymentMethod = document.getElementById('success-payment-method');
    const btnShareStruk = document.getElementById('btn-share-struk');
    const btnPesananBaru = document.getElementById('btn-pesanan-baru');
    const btnRiwayat = document.getElementById('btn-riwayat');

    const filterProductCards = () => {
        const keyword = (productSearchInput?.value || '').trim().toLowerCase();

        productCards.forEach((card) => {
            const cardName = card.getAttribute('data-product-name') || '';
            const cardCategory = card.getAttribute('data-product-category') || '';

            const matchKeyword = !keyword || cardName.includes(keyword);
            const matchCategory =
                activeCategory === 'semua produk' || cardCategory === activeCategory;

            card.classList.toggle('hidden', !(matchKeyword && matchCategory));
        });
    };

    categoryGroups.forEach((group) => {
        const tabs = group.querySelectorAll('[data-category-tab]');

        tabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                tabs.forEach((item) => {
                    item.classList.remove('is-active');
                    item.setAttribute('aria-pressed', 'false');
                });

                tab.classList.add('is-active');
                tab.setAttribute('aria-pressed', 'true');

                activeCategory = (
                    tab.getAttribute('data-category-value') ||
                    tab.textContent ||
                    'Semua produk'
                ).trim().toLowerCase();

                filterProductCards();
            });
        });
    });

    productSearchInput?.addEventListener('input', filterProductCards);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'F2') {
            event.preventDefault();
            productSearchInput?.focus();
        }
    });

    const showToast = (message) => {
        if (!toastContainer) return;

        toastContainer.innerHTML = `
            <div class="dashboard-success-toast">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-kasir-pastel text-kasir-primary">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M5 10.5 8.2 13.7 15 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-slate-900">${message}</p>
                </div>
            </div>
        `;

        toastContainer.classList.remove('hidden');

        if (toastTimeoutId) {
            window.clearTimeout(toastTimeoutId);
        }

        toastTimeoutId = window.setTimeout(() => {
            toastContainer.classList.add('hidden');
        }, 2000);
    };

    const showAddToast = () => showToast('Produk berhasil ditambahkan');
    const showCheckoutToast = () => showToast('Pembayaran berhasil');

    const showRecommendationToast = (count) => {
        if (count <= 0) return;
        showToast(count === 1 ? '1 rekomendasi ditambahkan' : `${count} rekomendasi ditambahkan`);
    };

    const getCartSubtotal = () => cart.reduce((sum, item) => sum + item.price * item.qty, 0);

    const extractProductFromButton = (button) => {
        const json = button.getAttribute('data-product');

        if (json) {
            try {
                return normalizeProduct(JSON.parse(json));
            } catch (error) {
                console.error('Gagal parse data-product:', error);
            }
        }

        return normalizeProduct({
            id: Number(button.getAttribute('data-id')),
            name: button.getAttribute('data-name') ?? 'Produk',
            sku: button.getAttribute('data-sku') ?? button.getAttribute('data-BRG') ?? '-',
            BRG: button.getAttribute('data-BRG') ?? button.getAttribute('data-sku') ?? '-',
            price: Number(button.getAttribute('data-price') ?? 0),
            stock: Number(button.getAttribute('data-stock') ?? 0),
            sold_count: Number(button.getAttribute('data-sold_count') ?? 0),
            category: button.getAttribute('data-category') ?? 'Lainnya',
        });
    };

    const isCartLocked = () => Boolean(activeOrder?.id);

    const syncAddButtonsState = () => {
        const locked = isCartLocked();

        addButtons.forEach((button) => {
            button.disabled = locked;
            button.classList.toggle('opacity-60', locked);
            button.classList.toggle('cursor-not-allowed', locked);
        });
    };

    const getCartQtyByProductId = (productId) => {
        const item = cart.find((cartItem) => cartItem.id === productId);
        return item ? item.qty : 0;
    };

    const canAddQty = (productId, nextQty) => {
        const product = productLookup.get(productId);
        if (!product) return true;
        return Number(product.stock) >= nextQty;
    };

    const resetPreCheckoutFlow = () => {
        if (activeOrder?.id) return;

        hasReviewedRecommendations = false;
        currentRecommendations = [];
        selectedRecommendationIds = new Set();
        recommendationModal?.classList.add('hidden');
        paymentModal?.classList.add('hidden');

        if (recommendationList) {
            recommendationList.innerHTML = '';
        }
    };

    const renderCart = () => {
        const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
        const subtotal = getCartSubtotal();
        const locked = isCartLocked();

        if (cartSummaryCount) {
            cartSummaryCount.textContent = `${totalItems} Item`;
        }

        if (cartSubtotal) {
            cartSubtotal.textContent = `Rp${formatRupiah(subtotal)}`;
        }

        if (cartTotal) {
            cartTotal.textContent = `Rp${formatRupiah(subtotal)}`;
        }

        if (currentOrderNumber) {
            currentOrderNumber.textContent = activeOrder?.order_number
                ? `Order: ${activeOrder.order_number}`
                : 'Belum ada order';
        }

        if (!cart.length) {
            cartEmpty?.classList.remove('hidden');
            cartContent?.classList.add('hidden');
            payButton?.setAttribute('disabled', 'true');
            payButton?.classList.add('opacity-60', 'cursor-not-allowed');

            if (cartItems) {
                cartItems.innerHTML = '';
            }

            syncAddButtonsState();
            updatePaymentSummary();
            return;
        }

        cartEmpty?.classList.add('hidden');
        cartContent?.classList.remove('hidden');

        if (!locked) {
            payButton?.removeAttribute('disabled');
            payButton?.classList.remove('opacity-60', 'cursor-not-allowed');
        } else {
            payButton?.setAttribute('disabled', 'true');
            payButton?.classList.add('opacity-60', 'cursor-not-allowed');
        }

        if (cartItems) {
            cartItems.innerHTML = cart
                .map(
                    (item) => `
                        <article class="dashboard-cart-item">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="truncate text-[15px] font-semibold text-slate-900">${item.name}</p>
                                    <p class="mt-1 text-[13px] text-slate-500">Rp ${formatRupiah(item.price)} X ${item.qty}</p>
                                    <p class="mt-2 text-[13px] font-semibold text-kasir-primary">Rp ${formatRupiah(item.price * item.qty)}</p>
                                </div>

                                <div class="flex flex-col items-end gap-3">
                                    <div class="flex items-center gap-2">
                                        <button
                                            type="button"
                                            data-cart-decrease="${item.id}"
                                            class="dashboard-qty-button ${locked ? 'opacity-60 cursor-not-allowed' : ''}"
                                            aria-label="Kurangi ${item.name}"
                                            ${locked ? 'disabled' : ''}
                                        >-</button>
                                        <span class="min-w-[12px] text-center text-[15px] font-semibold text-slate-700">${item.qty}</span>
                                        <button
                                            type="button"
                                            data-cart-increase="${item.id}"
                                            class="dashboard-qty-button ${locked ? 'opacity-60 cursor-not-allowed' : ''}"
                                            aria-label="Tambah ${item.name}"
                                            ${locked ? 'disabled' : ''}
                                        >+</button>
                                    </div>

                                    <button
                                        type="button"
                                        data-cart-delete="${item.id}"
                                        class="inline-flex h-7 w-7 items-center justify-center text-rose-500 transition hover:text-rose-600 ${locked ? 'opacity-60 cursor-not-allowed' : ''}"
                                        aria-label="Hapus ${item.name}"
                                        ${locked ? 'disabled' : ''}
                                    >
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                            <path d="M6.5 7.5v6M10 7.5v6M13.5 7.5v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            <path d="M3.5 5.5h13M7 3.5h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                            <path d="M5.5 5.5v9a1.5 1.5 0 0 0 1.5 1.5h6a1.5 1.5 0 0 0 1.5-1.5v-9" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </article>
                    `,
                )
                .join('');
        }

        syncAddButtonsState();
        updatePaymentSummary();
    };

    const hydrateCartFromOrder = (order) => {
        activeOrder = order ?? null;

        if (!order?.items) {
            renderCart();
            return;
        }

        cart = order.items.map((item) => ({
            id: Number(item.product_id),
            name: item.name,
            sku: item.sku ?? '-',
            price: Number(item.price),
            qty: Number(item.qty),
        }));

        renderCart();
    };

    const addProductToCart = (product) => {
        if (isCartLocked()) return;

        if (Number(product.stock) < 1) {
            alert(`Stok ${product.name} habis.`);
            return;
        }

        const nextQty = getCartQtyByProductId(product.id) + 1;

        if (!canAddQty(product.id, nextQty)) {
            alert(`Stok ${product.name} tidak mencukupi.`);
            return;
        }

        const existingItem = cart.find((item) => item.id === product.id);

        if (existingItem) {
            existingItem.qty += 1;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                sku: product.sku ?? product.BRG ?? '-',
                price: Number(product.price),
                qty: 1,
            });
        }

        resetPreCheckoutFlow();
        showAddToast();
        renderCart();
    };

    const buildRecommendationsFromCart = () => {
        const cartProductIds = cart.map((item) => item.id);
        const cartCategories = [
            ...new Set(
                cart.map((item) => productLookup.get(item.id)?.category).filter(Boolean),
            ),
        ];

        let relevant = allProducts
            .filter((product) => {
                return (
                    Number(product.stock) > 0 &&
                    !cartProductIds.includes(product.id) &&
                    cartCategories.includes(product.category)
                );
            })
            .sort((a, b) => Number(b.sold_count) - Number(a.sold_count))
            .slice(0, 2);

        if (relevant.length < 2) {
            const usedIds = new Set([...cartProductIds, ...relevant.map((item) => item.id)]);

            const fallback = allProducts
                .filter((product) => Number(product.stock) > 0 && !usedIds.has(product.id))
                .sort((a, b) => Number(b.sold_count) - Number(a.sold_count))
                .slice(0, 2 - relevant.length);

            relevant = [...relevant, ...fallback];
        }

        return relevant.slice(0, 2);
    };

    const renderRecommendationModal = () => {
        if (!recommendationList) return;

        recommendationList.innerHTML = currentRecommendations
            .slice(0, 2)
            .map((product) => {
                const selected = selectedRecommendationIds.has(product.id);
                const disabled = Number(product.stock) < 1;

                return `
                    <button
                        type="button"
                        data-recommendation-card="${product.id}"
                        class="dashboard-recommendation-item w-full text-left transition ${selected ? 'ring-2 ring-kasir-primary border-kasir-primary bg-kasir-pastel/20' : ''
                    } ${disabled ? 'opacity-60 cursor-not-allowed' : ''}"
                        ${disabled ? 'disabled' : ''}
                    >
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-4">
                                <div class="h-[60px] w-[60px] rounded-xl bg-slate-100 flex items-center justify-center text-slate-300 shadow-inner">
                                    <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[15px] font-bold text-slate-900 leading-tight">${product.name}</p>
                                    <p class="text-[12px] text-slate-500">${product.sku ?? product.BRG ?? '-'}</p>
                                    <p class="mt-1 text-[13px] font-bold text-kasir-primary">Rp ${formatRupiah(product.price)}</p>
                                    <p class="text-[11px] ${disabled ? 'text-red-500' : 'text-slate-500'}">
                                        ${disabled ? 'Stok habis' : `Stok: ${product.stock}`}
                                    </p>
                                </div>
                            </div>

                            <div class="flex h-7 w-7 items-center justify-center rounded-full border ${selected
                        ? 'border-kasir-primary bg-kasir-primary text-white'
                        : 'border-slate-300 bg-white text-slate-400'
                    }">
                                ${selected ? '✓' : ''}
                            </div>
                        </div>
                    </button>
                `;
            })
            .join('');
    };

    const showRecommendationModal = () => {
        currentRecommendations = buildRecommendationsFromCart();
        selectedRecommendationIds = new Set(currentRecommendations.map(p => p.id));

        if (!currentRecommendations.length) {
            hasReviewedRecommendations = true;
            return false;
        }

        renderRecommendationModal();
        recommendationModal?.classList.remove('hidden');
        return true;
    };

    const closeRecommendationModal = () => {
        recommendationModal?.classList.add('hidden');
        selectedRecommendationIds = new Set();

        if (recommendationList) {
            recommendationList.innerHTML = '';
        }
    };

    const toggleRecommendationSelection = (productId) => {
        const product = currentRecommendations.find((item) => item.id === productId);

        if (!product || Number(product.stock) < 1) return;

        if (selectedRecommendationIds.has(productId)) {
            selectedRecommendationIds.delete(productId);
        } else if (selectedRecommendationIds.size < 2) {
            selectedRecommendationIds.add(productId);
        }

        renderRecommendationModal();
    };

    const finalizeRecommendationStep = (selectedIds = []) => {
        let addedCount = 0;

        selectedIds.slice(0, 2).forEach((productId) => {
            const product = currentRecommendations.find((item) => item.id === productId);

            if (!product || Number(product.stock) < 1) return;

            const nextQty = getCartQtyByProductId(product.id) + 1;

            if (!canAddQty(product.id, nextQty)) return;

            const existingItem = cart.find((item) => item.id === product.id);

            if (existingItem) {
                existingItem.qty += 1;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    sku: product.sku ?? product.BRG ?? '-',
                    price: Number(product.price),
                    qty: 1,
                });
            }

            addedCount += 1;
        });

        hasReviewedRecommendations = true;
        currentRecommendations = [];
        closeRecommendationModal();
        renderCart();

        if (addedCount > 0) {
            showRecommendationToast(addedCount);
        }
    };

    const updatePaymentSummary = () => {
        const total = getCartSubtotal();

        paymentTotalTargets.forEach((el) => {
            el.textContent = `Rp ${formatRupiah(total)}`;
        });

        if (paymentAmountInput) {
            if (selectedPaymentMethod === 'QRIS') {
                paymentAmountInput.value = total;
                paymentAmountInput.setAttribute('readonly', 'true');
            } else {
                paymentAmountInput.removeAttribute('readonly');
            }
        }

        const paidAmount = Number(paymentAmountInput?.value || 0);
        const change = Math.max(0, paidAmount - total);

        paymentChangeTargets.forEach((el) => {
            el.textContent = `Rp ${formatRupiah(change)}`;
        });
    };

    const openPaymentModal = () => {
        if (!paymentModal) {
            alert('Popup pembayaran belum dibuat.');
            return;
        }

        updatePaymentSummary();
        paymentModal.classList.remove('hidden');
    };

    const checkoutCart = async () => {
        if (!cart.length || isProcessingCheckout || isCartLocked()) return;

        const total = getCartSubtotal();
        const paidAmount = Number(paymentAmountInput?.value || 0);

        if (selectedPaymentMethod === 'Tunai' && paidAmount < total) {
            alert('Jumlah bayar kurang dari total pembayaran.');
            return;
        }

        const checkoutUrl = window.posConfig?.checkoutUrl;

        if (!checkoutUrl) {
            alert('Route checkout belum tersedia.');
            return;
        }

        isProcessingCheckout = true;
        const currentTotal = getCartSubtotal();
        const currentMethod = selectedPaymentMethod;
        const currentPaid = paidAmount;
        const currentCartSnapshot = [...cart];

        try {
            const response = await fetch(checkoutUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken(),
                },
                body: JSON.stringify({
                    payment_method: selectedPaymentMethod,
                    paid_amount: paidAmount,
                    items: cart.map((item) => ({
                        product_id: item.id,
                        qty: item.qty,
                    })),
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || 'Pembayaran gagal diproses.');
            }

            hydrateCartFromOrder(data.order);
            paymentModal?.classList.add('hidden');
            paymentConfirmationModal?.classList.add('hidden');
            showSuccessModal(currentCartSnapshot, currentTotal, currentMethod, currentPaid);
        } catch (error) {
            console.error(error);
            alert(error.message || 'Terjadi kesalahan saat checkout.');
        } finally {
            isProcessingCheckout = false;
        }
    };

    const showSuccessModal = (cartSnapshot, totalBayar, method, paidAmount) => {
        if (!successModal) return;
        
        if (successTotalTagihan) successTotalTagihan.textContent = `Rp ${formatRupiah(totalBayar)}`;
        if (successPaymentMethod) successPaymentMethod.textContent = `Metode: ${method}`;
        
        if (method === 'Tunai') {
            if (successPaidContainer) successPaidContainer.classList.remove('hidden');
            if (successChangeContainer) successChangeContainer.classList.remove('hidden');
            
            if (successTunai) successTunai.textContent = `Rp ${formatRupiah(paidAmount)}`;
            
            const kembalian = Math.max(0, paidAmount - totalBayar);
            if (successKembalian) successKembalian.textContent = `Rp ${formatRupiah(kembalian)}`;
        } else {
            if (successPaidContainer) successPaidContainer.classList.add('hidden');
            if (successChangeContainer) successChangeContainer.classList.add('hidden');
        }
        
        if (successReceiptItems) {
            successReceiptItems.innerHTML = cartSnapshot.map(item => `
                <div class="flex justify-between items-center">
                    <span class="font-bold text-slate-700 text-[13px]">${item.name} (${item.qty}X)</span>
                    <span class="font-bold text-slate-900 text-[14px]">Rp ${formatRupiah(item.price * item.qty)}</span>
                </div>
            `).join('');
        }

        successModal.classList.remove('hidden');
    };

    const handlePayClick = () => {
        if (!cart.length || isProcessingCheckout || isCartLocked()) return;

        if (!hasReviewedRecommendations) {
            const isShown = showRecommendationModal();
            if (isShown) return;
        }

        openPaymentModal();
    };

    addButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const product = extractProductFromButton(button);
            addProductToCart(product);
        });
    });

    cartItems?.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target.closest('button') : null;

        if (!target || isCartLocked()) return;

        const increaseId = target.getAttribute('data-cart-increase');
        const decreaseId = target.getAttribute('data-cart-decrease');
        const deleteId = target.getAttribute('data-cart-delete');

        if (increaseId) {
            const nextQty = getCartQtyByProductId(Number(increaseId)) + 1;

            if (!canAddQty(Number(increaseId), nextQty)) {
                const product = productLookup.get(Number(increaseId));
                alert(`Stok ${product?.name ?? 'produk'} tidak mencukupi.`);
                return;
            }

            cart = cart.map((item) =>
                item.id === Number(increaseId) ? { ...item, qty: item.qty + 1 } : item,
            );

            resetPreCheckoutFlow();
            renderCart();
            return;
        }

        if (decreaseId) {
            cart = cart
                .map((item) =>
                    item.id === Number(decreaseId) ? { ...item, qty: item.qty - 1 } : item,
                )
                .filter((item) => item.qty > 0);

            resetPreCheckoutFlow();
            renderCart();
            return;
        }

        if (deleteId) {
            cart = cart.filter((item) => item.id !== Number(deleteId));
            resetPreCheckoutFlow();
            renderCart();
        }
    });

    paymentButtons.forEach((button) => {
        button.addEventListener('click', () => {
            selectedPaymentMethod = button.getAttribute('data-payment-method') ?? 'Tunai';
            syncPaymentMethodButtons();
            updatePaymentSummary();
        });
    });

    paymentQuickAmountButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const amount = Number(button.getAttribute('data-payment-quick-amount') || 0);

            if (paymentAmountInput) {
                paymentAmountInput.value = amount;
                updatePaymentSummary();
            }
        });
    });

    paymentAmountInput?.addEventListener('input', updatePaymentSummary);

    closePaymentButtons.forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            paymentModal?.classList.add('hidden');
        });
    });

    initiatePaymentButton?.addEventListener('click', (event) => {
        event.preventDefault();

        const total = getCartSubtotal();
        const paidAmount = Number(paymentAmountInput?.value || 0);

        if (selectedPaymentMethod === 'Tunai' && paidAmount < total) {
            alert('Jumlah bayar kurang dari total pembayaran.');
            return;
        }

        paymentConfirmationModal?.classList.remove('hidden');
    });

    closeConfirmationButton?.addEventListener('click', () => {
        paymentConfirmationModal?.classList.add('hidden');
    });

    confirmCheckoutButton?.addEventListener('click', (event) => {
        event.preventDefault();
        checkoutCart();
    });

    btnShareStruk?.addEventListener('click', () => {
        alert('Fitur share struk akan segera hadir.');
    });

    btnPesananBaru?.addEventListener('click', () => {
        successModal?.classList.add('hidden');
        clearCartButton?.click();
    });

    btnRiwayat?.addEventListener('click', () => {
        window.location.href = '/kasir/history';
    });

    payButton?.addEventListener('click', (event) => {
        event.preventDefault();
        handlePayClick();
    });

    document.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target.closest('button') : null;

        if (!target) return;

        if (target.hasAttribute('data-recommendation-card')) {
            event.preventDefault();
            const productId = Number(target.getAttribute('data-recommendation-card'));
            toggleRecommendationSelection(productId);
            return;
        }

        if (target.id === 'btn-rekomendasi-iya') {
            event.preventDefault();
            finalizeRecommendationStep([...selectedRecommendationIds]);
            return;
        }

        if (
            target.id === 'btn-rekomendasi-tidak' ||
            target.hasAttribute('data-close-recommendation')
        ) {
            event.preventDefault();
            finalizeRecommendationStep([]);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'F9') {
            event.preventDefault();

            const paymentVisible = paymentModal && !paymentModal.classList.contains('hidden');
            const confirmationVisible = paymentConfirmationModal && !paymentConfirmationModal.classList.contains('hidden');

            if (confirmationVisible) {
                checkoutCart();
                return;
            }

            if (paymentVisible) {
                const total = getCartSubtotal();
                const paidAmount = Number(paymentAmountInput?.value || 0);
        
                if (selectedPaymentMethod === 'Tunai' && paidAmount < total) {
                    alert('Jumlah bayar kurang dari total pembayaran.');
                    return;
                }
                paymentConfirmationModal?.classList.remove('hidden');
                return;
            }

            handlePayClick();
        }
    });

    clearCartButton?.addEventListener('click', () => {
        cart = [];
        activeOrder = null;
        currentRecommendations = [];
        selectedRecommendationIds = new Set();
        hasReviewedRecommendations = false;
        closeRecommendationModal();
        paymentModal?.classList.add('hidden');
        paymentConfirmationModal?.classList.add('hidden');
        renderCart();
    });

    syncPaymentMethodButtons();
    filterProductCards();
    renderCart();
});