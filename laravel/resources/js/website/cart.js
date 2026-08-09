document.addEventListener('DOMContentLoaded', () => {
    const itemsList = document.getElementById('cart-items-list');
    const emptyState = document.getElementById('empty-cart-state');
    const itemsCount = document.getElementById('items-count');
    const summarySubtotal = document.getElementById('summary-subtotal');
    const summaryTotal = document.getElementById('summary-total');
    const summaryDiscountRow = document.getElementById('summary-discount-row');
    const summaryDiscount = document.getElementById('summary-discount');
    const summaryDiscountLabel = document.getElementById('summary-discount-label');
    const checkoutBtn = document.getElementById('checkout-btn');

    let appliedDiscount = 0;

    const money = (amount) => `${amount.toFixed(2)}`;

    function recomputeTotals() {
        let subtotal = 0;
        let count = 0;

        document.querySelectorAll('.web-cart-item').forEach((row) => {
            const price = parseFloat(row.dataset.price || '0');
            const qty = parseInt(row.querySelector('.qty-input')?.value || '0', 10);
            const lineTotal = price * qty;
            const lineTotalEl = row.querySelector('[data-line-total]');

            if (lineTotalEl) {
                lineTotalEl.textContent = money(lineTotal);
            }

            subtotal += lineTotal;
            count += qty;
        });

        if (itemsCount) itemsCount.textContent = count;
        if (summarySubtotal) summarySubtotal.textContent = money(subtotal);
        if (summaryTotal) summaryTotal.textContent = money(Math.max(0, subtotal - appliedDiscount));

        if (count === 0) {
            itemsList?.classList.add('hidden');
            emptyState?.classList.remove('hidden');
            checkoutBtn?.classList.add('pointer-events-none', 'opacity-50');
        }
    }

    function removeRow(key) {
        const row = document.querySelector(`.web-cart-item[data-key="${CSS.escape(key)}"]`);
        row?.remove();
        recomputeTotals();
    }

    // Quantity steppers.
    document.addEventListener('click', (e) => {
        const minus = e.target.closest('.qty-minus');
        const plus = e.target.closest('.qty-plus');
        const btn = minus || plus;

        if (!btn) return;

        const key = btn.dataset.key;
        const row = btn.closest('.web-cart-item');
        const input = row.querySelector('.qty-input');
        let qty = parseInt(input.value || '1', 10);

        qty = minus ? Math.max(1, qty - 1) : qty + 1;
        input.value = qty;

        window.WebsiteApi.updateCartItem(key, qty).then((res) => {
            if (res.success) {
                recomputeTotals();
            }
        });
    });

    document.querySelectorAll('.qty-input').forEach((input) => {
        input.addEventListener('change', () => {
            let qty = parseInt(input.value || '1', 10);

            if (Number.isNaN(qty) || qty < 1) qty = 1;

            input.value = qty;

            window.WebsiteApi.updateCartItem(input.dataset.key, qty).then((res) => {
                if (res.success) recomputeTotals();
            });
        });
    });

    // Remove item.
    document.querySelectorAll('[data-cart-remove]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const key = btn.dataset.key;

            window.WebsiteApi.removeCartItem(key).then((res) => {
                if (res.success) removeRow(key);
            });
        });
    });

    // Clear cart.
    document.getElementById('clear-cart-btn')?.addEventListener('click', () => {
        window.WebsiteApi.clearCart().then((res) => {
            if (res.success) {
                document.querySelectorAll('.web-cart-item').forEach((row) => row.remove());
                recomputeTotals();
            }
        });
    });

    // Coupon.
    document.getElementById('coupon-apply-btn')?.addEventListener('click', () => {
        const input = document.getElementById('coupon-input');
        const message = document.getElementById('coupon-message');
        const code = input?.value?.trim();

        if (!code) return;

        window.WebsiteApi.applyCoupon(code).then((res) => {
            if (!message) return;

            message.classList.remove('hidden', 'is-success', 'is-error');

            if (res.valid) {
                const subtotal = parseFloat(summarySubtotal?.textContent || '0');
                appliedDiscount = res.type === 'percentage' ? (subtotal * res.value) / 100 : res.value;

                summaryDiscountRow?.classList.remove('hidden');
                if (summaryDiscount) summaryDiscount.textContent = `−${money(appliedDiscount)}`;
                if (summaryDiscountLabel) summaryDiscountLabel.textContent = `(${code})`;

                message.classList.add('is-success');
                message.textContent = window.translations?.couponApplied || 'Coupon applied successfully.';
                recomputeTotals();
            } else {
                appliedDiscount = 0;
                summaryDiscountRow?.classList.add('hidden');
                message.classList.add('is-error');
                message.textContent = res.message || 'Invalid coupon code.';
                recomputeTotals();
            }
        });
    });
});
