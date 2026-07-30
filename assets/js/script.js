/* ellamart+ — site scripts (Swiper carousels, WOW.js reveals, UI interactions) */

document.addEventListener('DOMContentLoaded', function () {

  /* ---------------- WOW.js scroll animations ---------------- */
  if (window.WOW) {
    new WOW({ boxClass: 'wow', offset: 60, mobile: true, live: true }).init();
  }

  /* ---------------- Mobile drawer menu ---------------- */
  const menuBtn = document.getElementById('menu-toggle');
  const mobileMenu = document.getElementById('mobile-menu');
  const mobileOverlay = document.getElementById('mobile-overlay');
  const menuClose = document.getElementById('menu-close');

  function openMenu() {
    mobileMenu.classList.add('open');
    mobileOverlay.classList.add('open');
    document.body.classList.add('overflow-hidden');
  }
  function closeMenu() {
    mobileMenu.classList.remove('open');
    mobileOverlay.classList.remove('open');
    document.body.classList.remove('overflow-hidden');
  }
  menuBtn && menuBtn.addEventListener('click', openMenu);
  menuClose && menuClose.addEventListener('click', closeMenu);
  mobileOverlay && mobileOverlay.addEventListener('click', closeMenu);

  /* ---------------- Mobile accordion submenus ---------------- */
  document.querySelectorAll('.mobile-acc-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const panel = btn.nextElementSibling;
      const icon = btn.querySelector('.acc-icon');
      panel.classList.toggle('hidden');
      icon && icon.classList.toggle('rotate-180');
    });
  });

  /* ---------------- Search overlay ---------------- */
  const searchBtn = document.getElementById('search-toggle');
  const searchBox = document.getElementById('search-overlay');
  const searchClose = document.getElementById('search-close');
  searchBtn && searchBtn.addEventListener('click', function () {
    searchBox.classList.remove('hidden');
    setTimeout(() => document.getElementById('search-input').focus(), 100);
  });
  searchClose && searchClose.addEventListener('click', function () {
    searchBox.classList.add('hidden');
  });

  /* ---------------- Sticky header shadow on scroll ---------------- */
  const header = document.getElementById('site-header');
  const backToTop = document.getElementById('back-to-top');
  window.addEventListener('scroll', function () {
    if (window.scrollY > 40) {
      header.classList.add('shadow-md');
    } else {
      header.classList.remove('shadow-md');
    }
    if (backToTop) {
      if (window.scrollY > 500) {
        backToTop.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
      } else {
        backToTop.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
      }
    }
  });
  backToTop && backToTop.addEventListener('click', function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });

  /* ---------------- Swiper carousels ---------------- */
  const commonNavPagination = (selector) => ({
    nextEl: selector + ' .swiper-button-next',
    prevEl: selector + ' .swiper-button-prev',
  });

  function initSwiper(selector, options) {
    const el = document.querySelector(selector);
    if (!el) return null;
    return new Swiper(selector, Object.assign({
      slidesPerView: 2,
      spaceBetween: 16,
      navigation: commonNavPagination(selector),
      pagination: { el: selector + ' .swiper-pagination', clickable: true },
    }, options));
  }

  initSwiper('.new-product-swiper', {
    slidesPerView: 2,
    breakpoints: { 640: { slidesPerView: 3 }, 1024: { slidesPerView: 4 } },
  });

  initSwiper('.featured-product-swiper', {
    slidesPerView: 2,
    breakpoints: { 640: { slidesPerView: 3 }, 1024: { slidesPerView: 4 } },
  });

  initSwiper('.brand-swiper', {
    slidesPerView: 2,
    spaceBetween: 20,
    breakpoints: {
      480: { slidesPerView: 3 },
      768: { slidesPerView: 4 },
      1024: { slidesPerView: 5 },
    },
  });

  initSwiper('.top-sellers-swiper', {
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      480: { slidesPerView: 2 },
      768: { slidesPerView: 3 },
      1024: { slidesPerView: 4 },
      1280: { slidesPerView: 6 },
    },
  });

  initSwiper('.clothing-swiper', {
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      640: { slidesPerView: 3 },
      1024: { slidesPerView: 4 },
    },
  });

  initSwiper('.furniture-swiper', {
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      640: { slidesPerView: 3 },
      1024: { slidesPerView: 4 },
    },
  });

  initSwiper('.electronics-swiper', {
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      640: { slidesPerView: 3 },
      1024: { slidesPerView: 4 },
    },
  });

  initSwiper('.furniture2-swiper', {
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      640: { slidesPerView: 3 },
      1024: { slidesPerView: 4 },
    },
  });

  initSwiper('.deal-swiper', {
    slidesPerView: 1,
    spaceBetween: 0,
    loop: true,
    autoplay: { delay: 4000, disableOnInteraction: false },
  });

  initSwiper('.recommended-swiper', {
    slidesPerView: 2,
    spaceBetween: 16,
    breakpoints: {
      480: { slidesPerView: 2 },
      768: { slidesPerView: 3 },
      1024: { slidesPerView: 4 },
    },
  });

  /* ---------------- Countdown timer (Only Today banner) ---------------- */
  const countdownEl = document.getElementById('deal-countdown');
  if (countdownEl) {
    let end = new Date();
    end.setHours(23, 59, 59, 999);
    setInterval(function () {
      const now = new Date();
      let diff = Math.max(0, end - now);
      const h = String(Math.floor(diff / 3600000)).padStart(2, '0');
      const m = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
      const s = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
      countdownEl.textContent = h + ':' + m + ':' + s;
    }, 1000);
  }

  /* ---------------- Newsletter form ---------------- */
  const newsletterForm = document.getElementById('newsletter-form');
  newsletterForm && newsletterForm.addEventListener('submit', function (e) {
    e.preventDefault();
    const input = newsletterForm.querySelector('input[type="email"]');
    const msg = document.getElementById('newsletter-msg');
    if (input.value) {
      msg.textContent = 'Thanks for subscribing!';
      msg.classList.remove('hidden');
      input.value = '';
    }
  });

  /* ---------------- Current year in footer ---------------- */
  document.querySelectorAll('#current-year, .current-year').forEach(function (el) {
    el.textContent = new Date().getFullYear();
  });

});
