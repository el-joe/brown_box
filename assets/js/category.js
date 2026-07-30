/* ellamart+ — category page scripts (category swiper, filters sidebar, chips, price slider) */

document.addEventListener('DOMContentLoaded', function () {

  /* ---------------- Category swiper (rounded images, arrows only) ---------------- */
  if (window.Swiper && document.querySelector('.category-swiper')) {
    new Swiper('.category-swiper', {
      slidesPerView: 3,
      spaceBetween: 16,
      navigation: {
        nextEl: '.category-swiper .swiper-button-next',
        prevEl: '.category-swiper .swiper-button-prev',
      },
      breakpoints: {
        480: { slidesPerView: 4 },
        768: { slidesPerView: 5 },
        1024: { slidesPerView: 6 },
      },
    });
  }

  /* ---------------- Mobile filters drawer ---------------- */
  const filtersPanel = document.getElementById('filters-panel');
  const filtersOverlay = document.getElementById('filters-overlay');
  const filtersOpen = document.getElementById('filters-open');
  const filtersClose = document.getElementById('filters-close');
  const filtersApply = document.getElementById('filters-apply');

  function openFilters() {
    filtersPanel.classList.add('open');
    filtersOverlay.classList.add('open');
    document.body.classList.add('overflow-hidden');
  }
  function closeFilters() {
    filtersPanel.classList.remove('open');
    filtersOverlay.classList.remove('open');
    document.body.classList.remove('overflow-hidden');
  }
  filtersOpen && filtersOpen.addEventListener('click', openFilters);
  filtersClose && filtersClose.addEventListener('click', closeFilters);
  filtersOverlay && filtersOverlay.addEventListener('click', closeFilters);
  filtersApply && filtersApply.addEventListener('click', closeFilters);

  /* ---------------- Filter accordions ---------------- */
  document.querySelectorAll('.filter-acc-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const panel = btn.nextElementSibling;
      const icon = btn.querySelector('.acc-icon');
      panel.classList.toggle('hidden');
      icon && icon.classList.toggle('fa-chevron-up');
      icon && icon.classList.toggle('fa-chevron-down');
    });
  });

  /* ---------------- Price range slider ---------------- */
  const priceRange = document.getElementById('price-range');
  const priceOutput = document.getElementById('price-output');
  function updatePriceSlider() {
    if (!priceRange) return;
    const min = Number(priceRange.min);
    const max = Number(priceRange.max);
    const val = Number(priceRange.value);
    const pct = ((val - min) / (max - min)) * 100;
    priceRange.style.background =
      'linear-gradient(to right, var(--brand-blue) ' + pct + '%, #e6e9ef ' + pct + '%)';
    if (priceOutput) priceOutput.textContent = 'Up to $' + val;
  }
  priceRange && priceRange.addEventListener('input', updatePriceSlider);
  updatePriceSlider();

  /* ---------------- Color swatch toggle ---------------- */
  document.querySelectorAll('.color-swatch').forEach(function (swatch) {
    swatch.addEventListener('click', function () {
      swatch.classList.toggle('active');
      syncActiveChips();
    });
  });

  /* ---------------- Active filter chips ---------------- */
  const chipsWrap = document.getElementById('active-chips');
  const resultsCount = document.getElementById('results-count');
  const TOTAL_PRODUCTS = 12;

  function getActiveFilters() {
    const filters = [];
    document.querySelectorAll('.filter-check input[type="checkbox"]:checked').forEach(function (cb) {
      const label = cb.dataset.filterLabel || cb.closest('[data-filter-label]')?.dataset.filterLabel || cb.parentElement.textContent.trim();
      filters.push({ label: label, source: cb });
    });
    document.querySelectorAll('.color-swatch.active').forEach(function (sw) {
      filters.push({ label: sw.dataset.filterLabel, source: sw });
    });
    return filters;
  }

  function syncActiveChips() {
    const active = getActiveFilters();
    if (!chipsWrap) return;
    chipsWrap.innerHTML = '';
    if (active.length === 0) {
      chipsWrap.classList.add('hidden');
    } else {
      chipsWrap.classList.remove('hidden');
      active.forEach(function (f) {
        const chip = document.createElement('span');
        chip.className = 'filter-chip';
        chip.innerHTML = f.label + ' <button aria-label="Remove ' + f.label + '"><i class="fa-solid fa-xmark"></i></button>';
        chip.querySelector('button').addEventListener('click', function () {
          if (f.source.tagName === 'INPUT') {
            f.source.checked = false;
          } else {
            f.source.classList.remove('active');
          }
          syncActiveChips();
        });
        chipsWrap.appendChild(chip);
      });
    }
    // Fake, illustrative result count based on active filter count
    if (resultsCount) {
      const reduced = Math.max(1, TOTAL_PRODUCTS - active.length * 2);
      resultsCount.textContent = active.length ? reduced : TOTAL_PRODUCTS;
    }
  }

  document.querySelectorAll('.filter-check input[type="checkbox"]').forEach(function (cb) {
    cb.addEventListener('change', syncActiveChips);
  });

  /* ---------------- Clear all filters ---------------- */
  const clearBtn = document.getElementById('clear-filters');
  clearBtn && clearBtn.addEventListener('click', function () {
    document.querySelectorAll('.filter-check input[type="checkbox"]').forEach(function (cb) { cb.checked = false; });
    document.querySelectorAll('.color-swatch.active').forEach(function (sw) { sw.classList.remove('active'); });
    if (priceRange) {
      priceRange.value = priceRange.max;
      updatePriceSlider();
    }
    syncActiveChips();
  });

  /* ---------------- Pagination (visual only) ---------------- */
  document.querySelectorAll('.page-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      if (btn.classList.contains('page-btn-active')) return;
      if (!/^\d+$/.test(btn.textContent.trim())) return;
      document.querySelectorAll('.page-btn-active').forEach(function (b) { b.classList.remove('page-btn-active'); });
      btn.classList.add('page-btn-active');
      document.getElementById('products-grid').scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

});
