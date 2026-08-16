// Home page behaviour: flash sale countdown timer + Swiper carousels.
import Swiper from 'swiper';
import { Autoplay, Navigation, Pagination } from 'swiper/modules';

function startCountdown(container) {
    const endsAt = container.dataset.flashEnds;

    if (!endsAt) {
        return;
    }

    const end = new Date(endsAt).getTime();
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

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((distance / (1000 * 60)) % 60);
        const seconds = Math.floor((distance / 1000) % 60);

        const pad = (n) => String(n).padStart(2, '0');

        if (boxes.days) boxes.days.textContent = pad(days);
        if (boxes.hours) boxes.hours.textContent = pad(hours);
        if (boxes.minutes) boxes.minutes.textContent = pad(minutes);
        if (boxes.seconds) boxes.seconds.textContent = pad(seconds);
    };

    tick();
    const timer = setInterval(tick, 1000);
}

const productSwiperOptions = {
    modules: [Navigation],
    spaceBetween: 16,
    slidesPerView: 2,
    breakpoints: {
        480: { slidesPerView: 2 },
        640: { slidesPerView: 3 },
        768: { slidesPerView: 4 },
        1024: { slidesPerView: 5 },
        1280: { slidesPerView: 6 },
    },
};

function initSwiper(el, options) {
    return new Swiper(el, {
        navigation: {
            nextEl: el.querySelector('.swiper-button-next'),
            prevEl: el.querySelector('.swiper-button-prev'),
        },
        ...options,
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const flashSection = document.querySelector('[data-flash-ends]');

    if (flashSection) {
        startCountdown(flashSection);
    }

    const heroSwiperEl = document.querySelector('.hero-swiper');

    if (heroSwiperEl) {
        initSwiper(heroSwiperEl, {
            modules: [Navigation, Pagination, Autoplay],
            loop: heroSwiperEl.querySelectorAll('.swiper-slide').length > 1,
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: { el: heroSwiperEl.querySelector('.swiper-pagination'), clickable: true },
        });
    }

    document.querySelectorAll('.web-product-swiper').forEach((el) => {
        initSwiper(el, productSwiperOptions);
    });

    const brandSwiperEl = document.querySelector('.brand-swiper');

    if (brandSwiperEl) {
        initSwiper(brandSwiperEl, {
            spaceBetween: 24,
            slidesPerView: 2,
            breakpoints: {
                480: { slidesPerView: 3 },
                768: { slidesPerView: 4 },
                1024: { slidesPerView: 6 },
            },
        });
    }
});
