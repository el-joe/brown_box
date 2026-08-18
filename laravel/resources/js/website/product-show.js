// Product detail page behaviour: gallery, variant selector, quantity stepper,
// add-to-cart / wishlist ajax, flash sale countdown, description/specs/reviews tabs
// and the related-products carousel.
import Swiper from 'swiper';
import { Navigation, Thumbs, Zoom } from 'swiper/modules';

function initRelatedSwiper() {
    document.querySelectorAll('.web-product-swiper').forEach((el) => {
        new Swiper(el, {
            modules: [Navigation],
            spaceBetween: 16,
            slidesPerView: 2,
            navigation: {
                nextEl: el.querySelector('.swiper-button-next'),
                prevEl: el.querySelector('.swiper-button-prev'),
            },
            breakpoints: {
                480: { slidesPerView: 2 },
                640: { slidesPerView: 3 },
                768: { slidesPerView: 4 },
                1024: { slidesPerView: 5 },
            },
        });
    });
}

function moneyFormat(amount) {
    return new Intl.NumberFormat(document.documentElement.lang === 'ar' ? 'ar-EG' : 'en-US', {
        style: 'currency',
        currency: 'EGP',
        minimumFractionDigits: 2,
    }).format(amount);
}

// ── Gallery: main slider synced to thumbnail strip ──────────────────────────
let gallerySwiper = null; // exposed so initVariants can slideTo()

function initGallery(root) {
    const mainEl = root.querySelector('.product-gallery-main');
    const thumbEl = root.querySelector('.product-gallery-thumbs');

    if (!mainEl) return;

    // Thumbs swiper (optional — only present when gallery.count() > 1)
    let thumbsSwiper = null;
    if (thumbEl) {
        thumbsSwiper = new Swiper(thumbEl, {
            modules: [Navigation],
            slidesPerView: 'auto',
            spaceBetween: 10,
            watchSlidesProgress: true,
            freeMode: true,
        });
    }

    // Main swiper
    gallerySwiper = new Swiper(mainEl, {
        modules: thumbsSwiper ? [Thumbs] : [],
        spaceBetween: 0,
        slidesPerView: 1,
        ...(thumbsSwiper ? { thumbs: { swiper: thumbsSwiper } } : {}),
        on: {
            // Keep active thumb highlighted with brand border
            slideChange(swiper) {
                if (!thumbEl) return;
                thumbEl.querySelectorAll('.swiper-slide').forEach((slide, i) => {
                    slide.classList.toggle('!border-brand', i === swiper.activeIndex);
                    slide.classList.toggle('border-transparent', i !== swiper.activeIndex);
                });
            },
        },
    });

    // Highlight first thumb on init
    const firstThumb = thumbEl?.querySelector('.swiper-slide');
    if (firstThumb) firstThumb.classList.add('!border-brand');
}

function initQuantity(root) {
    const input = root.querySelector('#qty-input');
    const minusBtn = root.querySelector('#qty-minus');
    const plusBtn = root.querySelector('#qty-plus');
    const stock = parseInt(root.dataset.stock, 10) || 0;

    if (!input) {
        return { getQty: () => 1 };
    }

    const clamp = (value) => {
        let qty = parseInt(value, 10);

        if (Number.isNaN(qty) || qty < 1) {
            qty = 1;
        }

        if (stock > 0 && qty > stock) {
            qty = stock;
        }

        return qty;
    };

    input.addEventListener('change', () => {
        input.value = clamp(input.value);
    });

    minusBtn?.addEventListener('click', () => {
        input.value = clamp(parseInt(input.value, 10) - 1);
    });

    plusBtn?.addEventListener('click', () => {
        input.value = clamp(parseInt(input.value, 10) + 1);
    });

    return { getQty: () => clamp(input.value) };
}

function initVariants(root) {
    const hasVariants = root.dataset.hasVariants === '1';

    if (!hasVariants) {
        return { getSelectedVariantId: () => null };
    }

    let variants = [];
    try {
        variants = JSON.parse(root.dataset.variants || '[]');
    } catch (e) {
        variants = [];
    }

    const priceCurrent = root.querySelector('#product-price-current');
    const priceOriginal = root.querySelector('#product-price-original');
    const addToCartBtn = root.querySelector('#add-to-cart-btn');
    const buyNowBtn = root.querySelector('#buy-now-btn');

    const selected = {};

    const findMatchingVariant = () => variants.find((variant) => {
        return Object.entries(selected).every(([attrId, valueId]) => {
            return String(variant.attributes?.[attrId]) === String(valueId);
        });
    });

    const applyVariant = (variant) => {
        if (!variant) {
            return;
        }

        if (priceCurrent) {
            priceCurrent.textContent = moneyFormat(variant.price);
        }

        if (priceOriginal) {
            if (variant.compare_price && variant.compare_price > variant.price) {
                priceOriginal.textContent = moneyFormat(variant.compare_price);
                priceOriginal.classList.remove('hidden');
            } else {
                priceOriginal.classList.add('hidden');
            }
        }

        // When a variant has its own image, slide the gallery to the matching slide.
        // Fall back to slide 0 if no match found.
        if (variant.image && gallerySwiper) {
            const slides = gallerySwiper.el.querySelectorAll('.swiper-slide img');
            let targetIndex = 0;
            slides.forEach((img, i) => {
                if (img.src === variant.image || img.src.endsWith(variant.image)) {
                    targetIndex = i;
                }
            });
            gallerySwiper.slideTo(targetIndex);
        } else if (gallerySwiper) {
            gallerySwiper.slideTo(0);
        }

        const inStock = variant.stock > 0;
        [addToCartBtn, buyNowBtn].forEach((btn) => {
            if (btn) {
                btn.disabled = !inStock;
            }
        });
    };

    root.querySelectorAll('[data-attribute-group]').forEach((group) => {
        const pills = group.querySelectorAll('.web-variant-pill');
        const label = group.querySelector('[data-attribute-selected-label]');

        pills.forEach((pill) => {
            if (pill.classList.contains('is-active')) {
                selected[pill.dataset.attributeId] = pill.dataset.valueId;
            }

            pill.addEventListener('click', () => {
                pills.forEach((p) => p.classList.remove('is-active'));
                pill.classList.add('is-active');
                selected[pill.dataset.attributeId] = pill.dataset.valueId;

                if (label) {
                    label.textContent = pill.dataset.valueLabel;
                }

                applyVariant(findMatchingVariant());
            });
        });
    });

    // Apply the initial default combination on load.
    applyVariant(findMatchingVariant());

    return { getSelectedVariantId: () => findMatchingVariant()?.id ?? null };
}

function initActions(root, { getQty, getSelectedVariantId }) {
    const productId = parseInt(root.dataset.productId, 10);
    const addToCartBtn = root.querySelector('#add-to-cart-btn');
    const buyNowBtn = root.querySelector('#buy-now-btn');
    const wishlistBtn = root.querySelector('#wishlist-btn');
    const shareBtn = root.querySelector('#share-btn');

    const addToCart = () => window.WebsiteApi.addToCart(productId, getSelectedVariantId(), getQty());

    addToCartBtn?.addEventListener('click', () => {
        addToCart().then(() => {
            addToCartBtn.classList.add('opacity-75');
            setTimeout(() => addToCartBtn.classList.remove('opacity-75'), 400);
        });
    });

    buyNowBtn?.addEventListener('click', () => {
        addToCart().then(() => {
            window.location.href = window.routes?.cartAdd?.replace('cart/add', 'checkout') ?? '/';
        });
    });

    wishlistBtn?.addEventListener('click', () => {
        window.WebsiteApi.toggleWishlist(productId, getSelectedVariantId()).then((data) => {
            if (!data?.success) {
                return;
            }

            const wishlisted = data.wishlisted;
            const icon = wishlistBtn.querySelector('i');
            const label = wishlistBtn.querySelector('#wishlist-btn-label');

            if (icon) {
                icon.classList.toggle('fa-solid', wishlisted);
                icon.classList.toggle('fa-regular', !wishlisted);
                icon.classList.toggle('text-red-500', wishlisted);
            }

            if (label) {
                label.textContent = wishlisted
                    ? (wishlistBtn.dataset.labelAdd || 'Remove from Favorites')
                    : (wishlistBtn.dataset.labelRemove || 'Add to Favorites');
            }

            wishlistBtn.dataset.wishlisted = wishlisted ? '1' : '0';
            wishlistBtn.classList.toggle('is-wishlisted', wishlisted);
        });
    });

    shareBtn?.addEventListener('click', () => {
        const base    = window.location.href.split('?')[0];
        const refCode = window.routes?.affiliateCode;
        const url     = refCode ? `${base}?ref=${encodeURIComponent(refCode)}` : window.location.href;

        if (navigator.share) {
            navigator.share({ url, title: document.title }).catch(() => {});
        } else {
            navigator.clipboard.writeText(url).then(() => {
                toastr.success(
                    refCode
                        ? 'Affiliate link copied to clipboard!'
                        : 'Link copied to clipboard!'
                );
            }).catch(() => {
                window.prompt('Copy this link:', url);
            });
        }
    });
}

function initTabs(root) {
    const tabButtons = root.querySelectorAll('[data-tab]');
    const panels = root.querySelectorAll('[data-tab-panel]');

    tabButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            tabButtons.forEach((b) => b.classList.remove('is-active'));
            btn.classList.add('is-active');

            panels.forEach((panel) => {
                panel.classList.toggle('hidden', panel.dataset.tabPanel !== btn.dataset.tab);
            });
        });
    });
}

function initFlashCountdown(root) {
    const endsAt = root.dataset.flashEnds;

    if (!endsAt) {
        return;
    }

    const end = new Date(endsAt).getTime();
    const container = root.querySelector('#flash-countdown');

    if (!container) {
        return;
    }

    const boxes = {
        days: container.querySelector('[data-unit="days"]'),
        hours: container.querySelector('[data-unit="hours"]'),
        minutes: container.querySelector('[data-unit="minutes"]'),
        seconds: container.querySelector('[data-unit="seconds"]'),
    };

    const tick = () => {
        const distance = end - Date.now();

        if (distance <= 0) {
            Object.values(boxes).forEach((el) => el && (el.textContent = '00'));
            clearInterval(timer);
            return;
        }

        const pad = (n) => String(n).padStart(2, '0');

        if (boxes.days) boxes.days.textContent = pad(Math.floor(distance / 86400000));
        if (boxes.hours) boxes.hours.textContent = pad(Math.floor((distance / 3600000) % 24));
        if (boxes.minutes) boxes.minutes.textContent = pad(Math.floor((distance / 60000) % 60));
        if (boxes.seconds) boxes.seconds.textContent = pad(Math.floor((distance / 1000) % 60));
    };

    tick();
    const timer = setInterval(tick, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('.web-product');

    if (!root) {
        return;
    }

    initGallery(root);
    const { getQty } = initQuantity(root);
    const { getSelectedVariantId } = initVariants(root);
    initActions(root, { getQty, getSelectedVariantId });
    initTabs(root);
    initFlashCountdown(root);
    initRelatedSwiper();
});
