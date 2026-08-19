document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('checkout-form');

    if (!form) return;

    const steps = Array.from(form.querySelectorAll('[data-step]'));
    const indicators = Array.from(document.querySelectorAll('[data-step-indicator]'));
    let currentStep = 1;

    function showStep(step) {
        currentStep = step;

        steps.forEach((el) => {
            el.classList.toggle('hidden', Number(el.dataset.step) !== step);
        });

        indicators.forEach((el) => {
            const n = Number(el.dataset.stepIndicator);
            el.classList.toggle('is-active', n === step);
            el.classList.toggle('is-done', n < step);
        });

        window.scrollTo({ top: form.offsetTop - 100, behavior: 'smooth' });
    }

    function validateStep(step) {
        if (step === 1) {
            const selected = form.querySelector('input[name="address_id"]:checked');
            const newFormVisible = !document.getElementById('new-address-form').classList.contains('hidden');
            const errorEl = document.getElementById('address-error');

            if (!selected && !newFormVisible) {
                errorEl?.classList.remove('hidden');
                return false;
            }

            if (newFormVisible) {
                const required = ['name', 'phone', 'governorate_id', 'city_id', 'address_line'];
                const missing = required.some((id) => !document.getElementById(id)?.value);

                if (missing) {
                    errorEl?.classList.remove('hidden');
                    return false;
                }
            }

            errorEl?.classList.add('hidden');

            return true;
        }

        if (step === 3) {
            const selected = form.querySelector('input[name="payment_gateway"]:checked');
            const errorEl = document.getElementById('payment-error');

            if (!selected) {
                errorEl?.classList.remove('hidden');
                return false;
            }

            errorEl?.classList.add('hidden');

            return true;
        }

        return true;
    }

    form.querySelectorAll('[data-next-step]').forEach((btn) => {
        btn.addEventListener('click', () => {
            if (validateStep(currentStep)) {
                showStep(Math.min(3, currentStep + 1));

                if (currentStep === 2) {
                    loadShippingCompanies();
                }
            }
        });
    });

    form.querySelectorAll('[data-prev-step]').forEach((btn) => {
        btn.addEventListener('click', () => showStep(Math.max(1, currentStep - 1)));
    });

    // ---------- Address selection ----------
    const addressList = document.getElementById('address-list');
    const newAddressForm = document.getElementById('new-address-form');
    const addAddressBtn = document.getElementById('add-address-btn');

    addressList?.addEventListener('click', (e) => {
        const card = e.target.closest('[data-address-card]');

        if (!card) return;

        addressList.querySelectorAll('[data-address-card]').forEach((c) => c.classList.remove('is-checked'));
        card.classList.add('is-checked');
        card.querySelector('input[type="radio"]').checked = true;
        newAddressForm?.classList.add('hidden');
    });

    addAddressBtn?.addEventListener('click', () => {
        addressList?.querySelectorAll('[data-address-card]').forEach((c) => c.classList.remove('is-checked'));
        addressList?.querySelectorAll('input[name="address_id"]').forEach((i) => (i.checked = false));
        newAddressForm?.classList.remove('hidden');
    });

    // Initial tax calculation on page load.
    (function initTax() {
        const subtotal = parseFloat(document.querySelector('.web-checkout')?.dataset.subtotal || '0');
        const tax = updateTax(subtotal, 0);
        const totalEl = document.getElementById('summary-total');
        if (totalEl && tax > 0) totalEl.textContent = (subtotal + tax).toFixed(2);
    })();

    // Governorate -> city dependent dropdown.
    const govSelect = document.getElementById('governorate_id');
    const citySelect = document.getElementById('city_id');

    govSelect?.addEventListener('change', () => {
        const cities = window.checkoutData?.citiesByGovernorate?.[govSelect.value] || [];
        citySelect.innerHTML = '<option value="">—</option>';
        cities.forEach((city) => {
            const opt = document.createElement('option');
            opt.value = city.id;
            opt.textContent = city.name;
            citySelect.appendChild(opt);
        });
    });

    function selectedCityId() {
        const addressCard = addressList?.querySelector('[data-address-card].is-checked');

        if (addressCard) return addressCard.dataset.cityId;

        return citySelect?.value || null;
    }

    // ---------- Shipping ----------
    function loadShippingCompanies() {
        const container = document.getElementById('shipping-companies');
        const cityId = selectedCityId();

        if (!cityId) {
            container.innerHTML = `<p class="text-sm text-slate-400">${window.translations?.selectAddress || 'Please select a delivery address first.'}</p>`;
            return;
        }

        window.WebsiteApi.shippingCompanies(cityId).then((res) => {
            const rates = res.rates || [];

            if (rates.length === 0) {
                container.innerHTML = `<p class="text-sm text-slate-400">${window.translations?.free_shipping_area || 'Free shipping to your area.'}</p>`;
                document.getElementById('shipping-company-id-input').value = '';
                updateShippingCost(0);
                return;
            }

            container.innerHTML = rates
                .map(
                    (rate, idx) => `
                <label class="web-select-card ${idx === 0 ? 'is-checked' : ''}" data-shipping-card data-price="${rate.price}">
                    <input type="radio" name="shipping_company_display" value="${rate.shipping_company_id}" ${idx === 0 ? 'checked' : ''}>
                    <span class="web-select-check"><i class="fa-solid fa-check"></i></span>
                    <p class="font-bold text-sm text-slate-900">${rate.name}</p>
                    <p class="text-xs text-slate-400 mt-1">${rate.estimated_days ? rate.estimated_days + (window.translations?.days_suffix || ' days') : ''}</p>
                    <p class="text-sm font-semibold text-amber-600 mt-2">${rate.price.toFixed(2)}</p>
                </label>`
                )
                .join('');

            const first = rates[0];
            document.getElementById('shipping-company-id-input').value = first.shipping_company_id;
            updateShippingCost(first.price);
        });
    }

    document.getElementById('shipping-companies')?.addEventListener('click', (e) => {
        const card = e.target.closest('[data-shipping-card]');

        if (!card) return;

        document.querySelectorAll('[data-shipping-card]').forEach((c) => c.classList.remove('is-checked'));
        card.classList.add('is-checked');
        card.querySelector('input[type="radio"]').checked = true;
        document.getElementById('shipping-company-id-input').value = card.querySelector('input').value;
        updateShippingCost(parseFloat(card.dataset.price || '0'));
    });

    function updateShippingCost(cost) {
        const shippingEl = document.getElementById('summary-shipping');
        const totalEl = document.getElementById('summary-total');
        const subtotal = parseFloat(document.querySelector('.web-checkout')?.dataset.subtotal || '0');

        if (shippingEl) shippingEl.textContent = cost > 0 ? cost.toFixed(2) : (window.translations?.free || 'Free');

        const discount = parseFloat(document.getElementById('summary-discount')?.dataset.value || '0');
        const tax = updateTax(subtotal, discount);

        if (totalEl) totalEl.textContent = (subtotal - discount + cost + tax).toFixed(2);
    }

    // ---------- Tax ----------
    function updateTax(subtotal, discount) {
        const taxRate = parseFloat(document.querySelector('.web-checkout')?.dataset.taxRate || '0');
        const taxRow = document.getElementById('summary-tax-row');
        const taxEl = document.getElementById('summary-tax');
        const tax = Math.round((subtotal - discount) * (taxRate / 100) * 100) / 100;

        if (taxEl) taxEl.textContent = tax.toFixed(2);
        taxRow?.classList.toggle('hidden', !(taxRate > 0 && tax > 0));

        return tax;
    }

    // ---------- Payment method ----------
    const paymentMethods = document.getElementById('payment-methods');
    const paymentDetails = document.getElementById('payment-details');

    paymentMethods?.addEventListener('click', (e) => {
        const card = e.target.closest('[data-payment-card]');

        if (!card) return;

        paymentMethods.querySelectorAll('[data-payment-card]').forEach((c) => c.classList.remove('is-checked'));
        card.classList.add('is-checked');
        card.querySelector('input[type="radio"]').checked = true;

        const method = card.dataset.method;
        const isManual = method !== 'paymob';

        paymentDetails.classList.toggle('hidden', !isManual);
        paymentDetails.querySelectorAll('.pm-instructions').forEach((el) => el.classList.add('hidden'));
        document.getElementById(`pm-details-${method}`)?.classList.remove('hidden');

        const invoiceInput = document.getElementById('invoice-input');
        if (invoiceInput) invoiceInput.required = isManual;
    });

    // ---------- File dropzone ----------
    const dropzone = document.getElementById('file-dropzone');
    const invoiceInput = document.getElementById('invoice-input');
    const filePreview = document.getElementById('file-preview');
    const filePreviewName = document.getElementById('file-preview-name');
    const filePreviewSize = document.getElementById('file-preview-size');

    dropzone?.addEventListener('click', () => invoiceInput.click());

    invoiceInput?.addEventListener('change', () => {
        const file = invoiceInput.files?.[0];

        if (!file) return;

        filePreviewName.textContent = file.name;
        filePreviewSize.textContent = `${(file.size / 1024).toFixed(0)} KB`;
        filePreview.classList.remove('hidden');
        dropzone.classList.add('hidden');
    });

    document.getElementById('file-remove-btn')?.addEventListener('click', () => {
        invoiceInput.value = '';
        filePreview.classList.add('hidden');
        dropzone.classList.remove('hidden');
    });

    // ---------- Copy buttons ----------
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.web-copy-btn');

        if (!btn) return;

        navigator.clipboard?.writeText(btn.dataset.copy || '').then(() => {
            btn.classList.add('is-copied');
            setTimeout(() => btn.classList.remove('is-copied'), 1500);
        });
    });

    // ---------- Coupon ----------
    document.getElementById('coupon-apply-btn')?.addEventListener('click', () => {
        const input = document.getElementById('coupon-input');
        const message = document.getElementById('coupon-message');
        const code = input?.value?.trim();

        if (!code) return;

        window.WebsiteApi.applyCoupon(code).then((res) => {
            if (!message) return;

            message.classList.remove('hidden', 'is-success', 'is-error');

            const subtotal = parseFloat(document.querySelector('.web-checkout')?.dataset.subtotal || '0');
            const discountEl = document.getElementById('summary-discount');
            const discountRow = document.getElementById('summary-discount-row');

            if (res.valid) {
                const discount = res.type === 'percentage' ? (subtotal * res.value) / 100 : res.value;

                discountRow?.classList.remove('hidden');
                if (discountEl) {
                    discountEl.textContent = `−${discount.toFixed(2)}`;
                    discountEl.dataset.value = discount;
                }

                document.getElementById('coupon-code-input').value = code;
                message.classList.add('is-success');
                message.textContent = window.translations?.couponApplied || 'Coupon applied successfully.';
                updateShippingCost(0);
            } else {
                discountRow?.classList.add('hidden');
                if (discountEl) discountEl.dataset.value = 0;
                message.classList.add('is-error');
                message.textContent = res.message || window.translations?.invalid_coupon_code || 'Invalid coupon code.';
            }
        });
    });

    // ---------- Submit ----------
    form.addEventListener('submit', (e) => {
        if (!validateStep(3)) {
            e.preventDefault();
            return;
        }

        const selectedGateway = form.querySelector('input[name="payment_gateway"]:checked')?.value;

        if (selectedGateway === 'paymob') {
            // Real Paymob integration would redirect to the Paymob iframe here using a token
            // minted server-side from the order total (see CheckoutController::store TODO).
            // For now the form submits normally and the order is created with a pending
            // payment status awaiting the Paymob callback.
        }
    });
});
