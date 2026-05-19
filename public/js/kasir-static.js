(function () {
    const fallbackProducts = [
        { id: 1, name: "Piatos", category: "Snack", price: 12000, stock: 120, soldCount: 50 },
        { id: 2, name: "Pulpen", category: "Alat Tulis Kantor", price: 18000, stock: 33, soldCount: 15 },
        { id: 3, name: "Paracetamol", category: "Medicine", price: 15000, stock: 32, soldCount: 80 },
        { id: 4, name: "Le Mineral", category: "Minuman", price: 15000, stock: 3, soldCount: 99 },
        { id: 5, name: "Tehpucuk", category: "Minuman", price: 5000, stock: 120, soldCount: 85 },
        { id: 6, name: "Roma Kelapa", category: "Snack", price: 8000, stock: 81, soldCount: 34 },
        { id: 7, name: "Kecap", category: "Lainnya", price: 8000, stock: 32, soldCount: 25 },
    ];
    const products = (window.posProducts?.length ? window.posProducts : fallbackProducts).map((product) => ({
        id: Number(product.id),
        name: product.name || "Produk",
        sku: product.sku || product.code || `BRG-${product.id}`,
        category: product.category || "Lainnya",
        price: Number(product.price || product.selling_price || 0),
        stock: Number(product.stock || 0),
        soldCount: Number(product.soldCount || product.sold_count || 0),
    }));

    let cart = [];
    let activeCategory = "Semua produk";
    let selectedPaymentMethod = "Tunai";
    let selectedRecommendations = new Set();
    let currentRecommendations = [];
    let hasReviewedRecommendations = false;
    let toastTimer = null;

    const rupiah = (amount) => `Rp ${new Intl.NumberFormat("id-ID").format(Number(amount) || 0)}`;
    const compactRupiah = (amount) => `Rp${new Intl.NumberFormat("id-ID").format(Number(amount) || 0)}`;

    const productById = (id) => products.find((product) => product.id === Number(id));
    const subtotal = () => cart.reduce((sum, item) => sum + item.price * item.qty, 0);
    const totalItems = () => cart.reduce((sum, item) => sum + item.qty, 0);

    const iconPlus = '<svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M10 4.5v11M4.5 10h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>';
    const iconTrash = '<svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M6.5 7.5v6M10 7.5v6M13.5 7.5v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M3.5 5.5h13M7 3.5h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M5.5 5.5v9a1.5 1.5 0 0 0 1.5 1.5h6a1.5 1.5 0 0 0 1.5-1.5v-9" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>';
    const iconCheck = '<svg viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="M5 10.5 8.2 13.7 15 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>';

    function showToast(message) {
        const toast = document.querySelector("[data-toast]");
        if (!toast) return;

        toast.innerHTML = `
            <div class="toast-inner">
                <span class="icon-circle">${iconCheck}</span>
                <strong>${message}</strong>
            </div>
        `;
        toast.classList.remove("hidden");

        if (toastTimer) window.clearTimeout(toastTimer);
        toastTimer = window.setTimeout(() => toast.classList.add("hidden"), 1800);
    }

    function setupLogin() {
        document.querySelectorAll("[data-password-toggle]").forEach((button) => {
            const input = document.getElementById(button.dataset.target || "");
            const label = button.querySelector("[data-password-label]");
            if (!input) return;

            button.addEventListener("click", () => {
                const hidden = input.type === "password";
                input.type = hidden ? "text" : "password";
                button.setAttribute("aria-pressed", String(hidden));
                if (label) label.textContent = hidden ? "Hide" : "Show";
            });
        });

        document.querySelectorAll("[data-static-login]").forEach((form) => {
            form.addEventListener("submit", (event) => {
                event.preventDefault();
                window.location.href = "/kasir/dashboard";
            });
        });
    }

    function renderCategories() {
        const holder = document.querySelector("[data-category-tabs]");
        if (!holder) return;

        const categories = ["Semua produk", ...new Set(products.map((product) => product.category))];
        holder.innerHTML = categories
            .map((category) => `
                <button type="button" class="category-tab ${category === activeCategory ? "active btn-active" : ""}" data-category="${category}" aria-pressed="${category === activeCategory}">
                    ${category}
                </button>
            `)
            .join("");
    }

    function renderProducts() {
        const grid = document.querySelector("[data-product-grid]");
        const search = document.querySelector("[data-product-search]");
        if (!grid) return;

        const keyword = (search?.value || "").trim().toLowerCase();
        const visibleProducts = products.filter((product) => {
            const matchesKeyword = !keyword || product.name.toLowerCase().includes(keyword) || product.sku.toLowerCase().includes(keyword);
            const matchesCategory = activeCategory === "Semua produk" || product.category === activeCategory;
            return matchesKeyword && matchesCategory;
        });

        grid.innerHTML = visibleProducts
            .map((product) => `
                <article class="card">
                    <strong>${product.name}</strong><br>
                    <small>${product.sku}</small>
                    <p class="price">${rupiah(product.price)}</p>
                    <p class="stock ${product.stock <= 5 ? "low" : ""}">Stok: ${product.stock}</p>
                    <button type="button" class="add-btn" data-add-product="${product.id}" aria-label="Tambah ${product.name}">
                        ${iconPlus}
                    </button>
                </article>
            `)
            .join("");
    }

    function resetPreCheckout() {
        hasReviewedRecommendations = false;
        selectedRecommendations = new Set();
        currentRecommendations = [];
        document.querySelector("[data-recommendation-modal]")?.classList.add("hidden");
    }

    function getCartQty(productId) {
        return cart.find((item) => item.id === Number(productId))?.qty || 0;
    }

    function addProduct(productId) {
        const product = productById(productId);
        if (!product) return;

        const nextQty = getCartQty(product.id) + 1;
        if (nextQty > product.stock) {
            window.alert(`Stok ${product.name} tidak mencukupi.`);
            return;
        }

        const existing = cart.find((item) => item.id === product.id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id: product.id, name: product.name, sku: product.sku, price: product.price, qty: 1 });
        }

        resetPreCheckout();
        renderCart();
        showToast("Produk berhasil ditambahkan");
    }

    function renderCart() {
        const empty = document.querySelector("[data-cart-empty]");
        const content = document.querySelector("[data-cart-content]");
        const list = document.querySelector("[data-cart-items]");
        const subtotalTargets = document.querySelectorAll("[data-cart-subtotal]");
        const totalTargets = document.querySelectorAll("[data-cart-total]");
        const countTargets = document.querySelectorAll("[data-cart-count]");
        const payButton = document.querySelector("[data-open-payment]");

        subtotalTargets.forEach((target) => (target.textContent = compactRupiah(subtotal())));
        totalTargets.forEach((target) => (target.textContent = compactRupiah(subtotal())));
        countTargets.forEach((target) => (target.textContent = `${totalItems()} Item`));

        if (!cart.length) {
            empty?.classList.remove("hidden");
            content?.classList.add("hidden");
            if (list) list.innerHTML = "";
            payButton?.setAttribute("disabled", "true");
            updatePaymentSummary();
            return;
        }

        empty?.classList.add("hidden");
        content?.classList.remove("hidden");
        payButton?.removeAttribute("disabled");

        if (list) {
            list.innerHTML = cart
                .map((item) => `
                    <article class="cart-item">
                        <div>
                            <p class="cart-item-title">${item.name}</p>
                            <small>${rupiah(item.price)} x ${item.qty}</small>
                            <small><strong>${rupiah(item.price * item.qty)}</strong></small>
                        </div>
                        <div>
                            <div class="qty-controls">
                                <button type="button" class="qty-button" data-cart-decrease="${item.id}" aria-label="Kurangi ${item.name}">-</button>
                                <strong>${item.qty}</strong>
                                <button type="button" class="qty-button" data-cart-increase="${item.id}" aria-label="Tambah ${item.name}">+</button>
                            </div>
                            <button type="button" class="delete-button" data-cart-delete="${item.id}" aria-label="Hapus ${item.name}">
                                ${iconTrash}
                            </button>
                        </div>
                    </article>
                `)
                .join("");
        }

        updatePaymentSummary();
    }

    function updateCartQty(productId, direction) {
        const product = productById(productId);
        cart = cart
            .map((item) => {
                if (item.id !== Number(productId)) return item;
                const nextQty = item.qty + direction;
                if (product && nextQty > product.stock) {
                    window.alert(`Stok ${product.name} tidak mencukupi.`);
                    return item;
                }
                return { ...item, qty: nextQty };
            })
            .filter((item) => item.qty > 0);
        resetPreCheckout();
        renderCart();
    }

    function deleteCartItem(productId) {
        cart = cart.filter((item) => item.id !== Number(productId));
        resetPreCheckout();
        renderCart();
    }

    function clearCart() {
        cart = [];
        resetPreCheckout();
        closeModal("[data-payment-modal]");
        closeModal("[data-confirmation-modal]");
        renderCart();
    }

    function buildRecommendations() {
        const cartIds = cart.map((item) => item.id);
        const cartCategories = new Set(
            cart.map((item) => productById(item.id)?.category).filter(Boolean),
        );

        const relevant = products
            .filter((product) => product.stock > 0 && !cartIds.includes(product.id) && cartCategories.has(product.category))
            .sort((a, b) => b.soldCount - a.soldCount);

        const fallback = products
            .filter((product) => product.stock > 0 && !cartIds.includes(product.id) && !relevant.some((item) => item.id === product.id))
            .sort((a, b) => b.soldCount - a.soldCount);

        return [...relevant, ...fallback].slice(0, 2);
    }

    function renderRecommendations() {
        const list = document.querySelector("[data-recommendation-list]");
        if (!list) return;

        list.innerHTML = currentRecommendations
            .map((product) => {
                const active = selectedRecommendations.has(product.id);
                return `
                    <button type="button" class="recommendation-card ${active ? "active" : ""}" data-recommendation="${product.id}">
                        <div class="summary-row">
                            <div>
                                <strong>${product.name}</strong>
                                <p class="muted">${product.sku}</p>
                                <p class="price">${rupiah(product.price)}</p>
                            </div>
                            <strong>${active ? "Dipilih" : "Pilih"}</strong>
                        </div>
                    </button>
                `;
            })
            .join("");
    }

    function openRecommendations() {
        currentRecommendations = buildRecommendations();
        selectedRecommendations = new Set(currentRecommendations.map((product) => product.id));
        if (!currentRecommendations.length) {
            hasReviewedRecommendations = true;
            return false;
        }
        renderRecommendations();
        openModal("[data-recommendation-modal]");
        return true;
    }

    function finishRecommendations(addSelected) {
        const ids = addSelected ? [...selectedRecommendations] : [];
        ids.forEach((id) => addProductSilently(id));
        selectedRecommendations = new Set();
        currentRecommendations = [];
        hasReviewedRecommendations = true;
        closeModal("[data-recommendation-modal]");
        renderCart();
        if (ids.length) showToast(`${ids.length} rekomendasi ditambahkan`);
    }

    function addProductSilently(productId) {
        const product = productById(productId);
        if (!product || getCartQty(product.id) + 1 > product.stock) return;

        const existing = cart.find((item) => item.id === product.id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id: product.id, name: product.name, sku: product.sku, price: product.price, qty: 1 });
        }
    }

    function openModal(selector) {
        document.querySelector(selector)?.classList.remove("hidden");
    }

    function closeModal(selector) {
        document.querySelector(selector)?.classList.add("hidden");
    }

    function syncPaymentButtons() {
        document.querySelectorAll("[data-payment-method]").forEach((button) => {
            const active = button.dataset.paymentMethod === selectedPaymentMethod;
            button.classList.toggle("active", active);
            button.setAttribute("aria-pressed", String(active));
        });
    }

    function updatePaymentSummary() {
        const total = subtotal();
        const input = document.querySelector("[data-payment-amount]");
        const paid = Number(input?.value || 0);
        const change = Math.max(0, paid - total);

        document.querySelectorAll("[data-payment-total]").forEach((target) => (target.textContent = rupiah(total)));
        document.querySelectorAll("[data-payment-change]").forEach((target) => (target.textContent = rupiah(change)));

        if (input) {
            if (selectedPaymentMethod === "QRIS") {
                input.value = total;
                input.setAttribute("readonly", "true");
            } else {
                input.removeAttribute("readonly");
            }
        }
    }

    function handlePayIntent() {
        if (!cart.length) return;
        if (!hasReviewedRecommendations && openRecommendations()) return;
        updatePaymentSummary();
        openModal("[data-payment-modal]");
    }

    function initiatePayment() {
        const input = document.querySelector("[data-payment-amount]");
        const paid = Number(input?.value || 0);

        if (selectedPaymentMethod === "Tunai" && paid < subtotal()) {
            window.alert("Jumlah bayar kurang dari total pembayaran.");
            return;
        }

        openModal("[data-confirmation-modal]");
    }

    async function completePayment() {
        const input = document.querySelector("[data-payment-amount]");
        const paid = Number(input?.value || 0);
        const snapshot = cart.map((item) => ({ ...item }));
        const total = subtotal();

        try {
            const response = await fetch(window.posConfig?.checkoutUrl || "/kasir/orders", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": window.posConfig?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content || "",
                },
                body: JSON.stringify({
                    payment_method: selectedPaymentMethod,
                    payment_amount: selectedPaymentMethod === "QRIS" ? total : paid,
                    items: cart.map((item) => ({
                        product_id: item.id,
                        quantity: item.qty,
                    })),
                }),
            });

            const payload = await response.json();

            if (!response.ok) {
                throw new Error(payload.message || "Pembayaran gagal diproses.");
            }

            closeModal("[data-confirmation-modal]");
            closeModal("[data-payment-modal]");
            showSuccess(snapshot, total, selectedPaymentMethod, selectedPaymentMethod === "QRIS" ? total : paid, payload.receipt_url);
        } catch (error) {
            window.alert(error.message || "Pembayaran gagal diproses.");
        }
    }

    function showSuccess(items, total, method, paid, receiptUrl) {
        const receiptItems = document.querySelector("[data-success-items]");
        const totalTarget = document.querySelector("[data-success-total]");
        const paidTarget = document.querySelector("[data-success-paid]");
        const changeTarget = document.querySelector("[data-success-change]");
        const methodTarget = document.querySelector("[data-success-method]");
        const paidRow = document.querySelector("[data-success-paid-row]");
        const changeRow = document.querySelector("[data-success-change-row]");

        if (receiptItems) {
            receiptItems.innerHTML = items
                .map((item) => `
                    <div class="receipt-line">
                        <span>${item.name} (${item.qty}x)</span>
                        <span>${rupiah(item.price * item.qty)}</span>
                    </div>
                `)
                .join("");
        }

        if (totalTarget) totalTarget.textContent = rupiah(total);
        if (paidTarget) paidTarget.textContent = rupiah(paid);
        if (changeTarget) changeTarget.textContent = rupiah(Math.max(0, paid - total));
        if (methodTarget) methodTarget.textContent = `Metode: ${method}`;

        const cash = method === "Tunai";
        paidRow?.classList.toggle("hidden", !cash);
        changeRow?.classList.toggle("hidden", !cash);
        const shareButton = document.querySelector("[data-share-receipt]");
        if (shareButton && receiptUrl) shareButton.dataset.receiptUrl = receiptUrl;

        openModal("[data-success-modal]");
        showToast("Pembayaran berhasil");
    }

    function setupDashboard() {
        if (!document.querySelector("[data-product-grid]")) return;

        renderCategories();
        renderProducts();
        renderCart();
        syncPaymentButtons();

        document.querySelector("[data-product-search]")?.addEventListener("input", renderProducts);

        document.addEventListener("click", (event) => {
            const button = event.target instanceof Element ? event.target.closest("button") : null;
            if (!button) return;

            if (button.dataset.category) {
                activeCategory = button.dataset.category;
                renderCategories();
                renderProducts();
            }

            if (button.dataset.addProduct) addProduct(button.dataset.addProduct);
            if (button.dataset.cartIncrease) updateCartQty(button.dataset.cartIncrease, 1);
            if (button.dataset.cartDecrease) updateCartQty(button.dataset.cartDecrease, -1);
            if (button.dataset.cartDelete) deleteCartItem(button.dataset.cartDelete);
            if (button.matches("[data-clear-cart]")) clearCart();
            if (button.matches("[data-open-payment]")) handlePayIntent();
            if (button.matches("[data-close-payment]")) closeModal("[data-payment-modal]");
            if (button.matches("[data-close-confirmation]")) closeModal("[data-confirmation-modal]");
            if (button.matches("[data-initiate-payment]")) initiatePayment();
            if (button.matches("[data-confirm-payment]")) completePayment();
            if (button.matches("[data-new-order]")) {
                closeModal("[data-success-modal]");
                clearCart();
            }
            if (button.matches("[data-history-link]")) window.location.href = window.posConfig?.historyUrl || "/kasir/history";
            if (button.matches("[data-share-receipt]")) {
                if (button.dataset.receiptUrl) window.location.href = button.dataset.receiptUrl;
                else window.alert("Struk belum tersedia.");
            }
            if (button.matches("[data-close-recommendation]")) finishRecommendations(false);
            if (button.matches("[data-accept-recommendation]")) finishRecommendations(true);
            if (button.matches("[data-skip-recommendation]")) finishRecommendations(false);
            if (button.dataset.recommendation) {
                const id = Number(button.dataset.recommendation);
                if (selectedRecommendations.has(id)) {
                    selectedRecommendations.delete(id);
                } else {
                    selectedRecommendations.add(id);
                }
                renderRecommendations();
            }
            if (button.dataset.paymentMethod) {
                selectedPaymentMethod = button.dataset.paymentMethod;
                syncPaymentButtons();
                updatePaymentSummary();
            }
            if (button.dataset.quickAmount) {
                const input = document.querySelector("[data-payment-amount]");
                if (input) input.value = button.dataset.quickAmount;
                updatePaymentSummary();
            }
        });

        document.querySelector("[data-payment-amount]")?.addEventListener("input", updatePaymentSummary);

        document.addEventListener("keydown", (event) => {
            if (event.key === "F2") {
                event.preventDefault();
                document.querySelector("[data-product-search]")?.focus();
            }
            if (event.key === "F9") {
                event.preventDefault();
                const confirmVisible = !document.querySelector("[data-confirmation-modal]")?.classList.contains("hidden");
                const paymentVisible = !document.querySelector("[data-payment-modal]")?.classList.contains("hidden");
                if (confirmVisible) completePayment();
                else if (paymentVisible) initiatePayment();
                else handlePayIntent();
            }
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
        setupLogin();
        setupDashboard();
    });
})();
