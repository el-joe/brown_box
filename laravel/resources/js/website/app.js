import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import toastr from 'toastr';
import 'toastr/build/toastr.css';

Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: 'toast-top-right',
    timeOut: 3000,
};
window.toastr = toastr;

function updateCartBadge(count) {
    const badge = document.getElementById('cart-count-badge');

    if (!badge) {
        return;
    }

    badge.textContent = count;
    badge.classList.toggle('hidden', !count);
}

function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function jsonFetch(url, options = {}) {
    return fetch(url, {
        ...options,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken(),
            Accept: 'application/json',
            ...(options.body ? { 'Content-Type': 'application/json' } : {}),
            ...options.headers,
        },
    }).then((response) => response.json());
}

window.WebsiteApi = {
    addToCart(productId, variantId = null, qty = 1) {
        return jsonFetch(window.routes?.cartAdd, {
            method: 'POST',
            body: JSON.stringify({ product_id: productId, variant_id: variantId, qty }),
        }).then((data) => {
            if (data.success) {
                updateCartBadge(data.cart_count);
                toastr.success(data.message || 'Added to cart successfully.');
            } else {
                toastr.error(data.message || 'Could not add product to cart.');
            }

            return data;
        });
    },
    toggleWishlist(productId, variantId = null) {
        return jsonFetch(window.routes?.wishlistToggle, {
            method: 'POST',
            body: JSON.stringify({ product_id: productId, variant_id: variantId }),
        });
    },
    updateCartItem(key, qty) {
        const url = window.routes?.cartUpdate?.replace('__KEY__', encodeURIComponent(key));

        return jsonFetch(url, {
            method: 'PATCH',
            body: JSON.stringify({ qty }),
        });
    },
    removeCartItem(key) {
        const url = window.routes?.cartRemove?.replace('__KEY__', encodeURIComponent(key));

        return jsonFetch(url, { method: 'DELETE' });
    },
    clearCart() {
        return jsonFetch(window.routes?.cartClear, { method: 'DELETE' });
    },
    applyCoupon(code) {
        return jsonFetch(window.routes?.couponApply, {
            method: 'POST',
            body: JSON.stringify({ code }),
        });
    },
    shippingCompanies(cityId) {
        const url = new URL(window.routes?.shippingCompanies, window.location.origin);
        url.searchParams.set('city_id', cityId);

        return jsonFetch(url.toString());
    },
    uploadPaymentProof(orderId, formData) {
        const url = window.routes?.paymentUploadProof?.replace('__ORDER__', encodeURIComponent(orderId));

        return fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken(),
                Accept: 'application/json',
            },
            body: formData,
        }).then((response) => response.json());
    },
    searchSuggestions(query) {
        const url = new URL(window.routes?.searchSuggestions, window.location.origin);
        url.searchParams.set('q', query);

        return jsonFetch(url.toString());
    },
};

document.addEventListener('DOMContentLoaded', () => {
    // Sticky header on scroll.
    const header = document.querySelector('.web-header');

    if (header) {
        const toggleSticky = () => {
            header.classList.toggle('is-sticky', window.scrollY > 10);
        };

        toggleSticky();
        window.addEventListener('scroll', toggleSticky, { passive: true });
    }
});
