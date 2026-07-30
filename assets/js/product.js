/* ellamart+ — product detail page scripts (gallery, variants, cart, wishlist, share, accordion) */

document.addEventListener('DOMContentLoaded', function () {

  /* ---------------- Gallery: thumbs + main swiper ---------------- */
  var thumbsSwiper = null;
  if (window.Swiper && document.querySelector('.gallery-thumbs-swiper')) {
    thumbsSwiper = new Swiper('.gallery-thumbs-swiper', {
      slidesPerView: 'auto',
      spaceBetween: 6,
      freeMode: true,
      watchSlidesProgress: true,
    });
  }
  if (window.Swiper && document.querySelector('.gallery-main-swiper')) {
    new Swiper('.gallery-main-swiper', {
      spaceBetween: 10,
      autoHeight: true,
      navigation: {
        nextEl: '.gallery-main-swiper .swiper-button-next',
        prevEl: '.gallery-main-swiper .swiper-button-prev',
      },
      thumbs: { swiper: thumbsSwiper },
    });
  }

  /* ---------------- Related products swiper (arrows only) ---------------- */
  if (window.Swiper && document.querySelector('.related-swiper')) {
    new Swiper('.related-swiper', {
      slidesPerView: 2,
      spaceBetween: 16,
      navigation: {
        nextEl: '.related-swiper .swiper-button-next',
        prevEl: '.related-swiper .swiper-button-prev',
      },
      breakpoints: {
        480: { slidesPerView: 2 },
        768: { slidesPerView: 3 },
        1024: { slidesPerView: 4 },
        1280: { slidesPerView: 6 },
      },
    });
  }
  /* Note: .recommended-swiper is already initialized generically in script.js */

  /* ---------------- Color variant selection ---------------- */
  var selectedColorEl = document.getElementById('selected-color');
  document.querySelectorAll('.variant-color').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.variant-color').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      if (selectedColorEl) selectedColorEl.textContent = btn.dataset.color;
    });
  });

  /* ---------------- Storage variant selection (updates price) ---------------- */
  var selectedStorageEl = document.getElementById('selected-storage');
  var priceCurrentEl = document.getElementById('product-price-current');
  var priceOriginalEl = document.getElementById('product-price-original');
  var discountPillEl = document.getElementById('product-discount-pill');

  function formatUSD(value) {
    return '$' + Number(value).toFixed(2);
  }

  document.querySelectorAll('.variant-pill').forEach(function (btn) {
    btn.addEventListener('click', function () {
      document.querySelectorAll('.variant-pill').forEach(function (b) { b.classList.remove('active'); });
      btn.classList.add('active');
      if (selectedStorageEl) selectedStorageEl.textContent = btn.dataset.storage;

      var current = Number(btn.dataset.current);
      var original = Number(btn.dataset.original);
      if (priceCurrentEl) priceCurrentEl.textContent = formatUSD(current);
      if (priceOriginalEl) priceOriginalEl.textContent = formatUSD(original);
      if (discountPillEl && original > 0) {
        var pct = Math.round(((original - current) / original) * 100);
        discountPillEl.textContent = 'Save ' + pct + '%';
      }
    });
  });

  /* ---------------- Quantity stepper ---------------- */
  var qtyInput = document.getElementById('qty-input');
  var qtyMinus = document.getElementById('qty-minus');
  var qtyPlus = document.getElementById('qty-plus');
  var QTY_MIN = 1;
  var QTY_MAX = 10;

  function clampQty(val) {
    val = parseInt(val, 10);
    if (isNaN(val)) val = QTY_MIN;
    return Math.min(QTY_MAX, Math.max(QTY_MIN, val));
  }
  qtyMinus && qtyMinus.addEventListener('click', function () {
    qtyInput.value = clampQty(Number(qtyInput.value) - 1);
  });
  qtyPlus && qtyPlus.addEventListener('click', function () {
    qtyInput.value = clampQty(Number(qtyInput.value) + 1);
  });
  qtyInput && qtyInput.addEventListener('change', function () {
    qtyInput.value = clampQty(qtyInput.value);
  });

  /* ---------------- Cart toast + header cart badge ---------------- */
  var cartToast = document.getElementById('cart-toast');
  var cartToastMsg = document.getElementById('cart-toast-msg');
  var cartCountEl = document.getElementById('cart-count');
  var cartToastTimer = null;

  function showCartToast(message) {
    if (!cartToast) return;
    cartToastMsg.textContent = message;
    cartToast.classList.add('show');
    clearTimeout(cartToastTimer);
    cartToastTimer = setTimeout(function () {
      cartToast.classList.remove('show');
    }, 2500);
  }

  function addQtyToCartBadge() {
    if (!cartCountEl) return;
    var qty = clampQty(qtyInput ? qtyInput.value : 1);
    cartCountEl.textContent = String(Number(cartCountEl.textContent || '0') + qty);
  }

  var addToCartBtn = document.getElementById('add-to-cart-btn');
  addToCartBtn && addToCartBtn.addEventListener('click', function () {
    addQtyToCartBadge();
    showCartToast('Added to cart!');
  });

  var buyNowBtn = document.getElementById('buy-now-btn');
  buyNowBtn && buyNowBtn.addEventListener('click', function () {
    addQtyToCartBadge();
    showCartToast('Proceeding to checkout...');
  });

  /* ---------------- Wishlist toggle ---------------- */
  var wishlistBtn = document.getElementById('wishlist-btn');
  var wishlistCountEl = document.getElementById('wishlist-count');
  wishlistBtn && wishlistBtn.addEventListener('click', function () {
    var isActive = wishlistBtn.classList.toggle('active');
    var icon = wishlistBtn.querySelector('i');
    icon.classList.toggle('fa-regular', !isActive);
    icon.classList.toggle('fa-solid', isActive);
    wishlistBtn.lastChild.textContent = isActive ? ' Added to Favorites' : ' Add to Favorites';
    if (wishlistCountEl) {
      wishlistCountEl.textContent = String(Number(wishlistCountEl.textContent || '0') + (isActive ? 1 : -1));
    }
  });

  /* ---------------- Share popover ---------------- */
  var shareBtn = document.getElementById('share-btn');
  var sharePopover = document.getElementById('share-popover');
  shareBtn && shareBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    sharePopover.classList.toggle('hidden');
  });
  document.addEventListener('click', function (e) {
    if (sharePopover && !sharePopover.classList.contains('hidden') && !sharePopover.contains(e.target) && e.target !== shareBtn) {
      sharePopover.classList.add('hidden');
    }
  });

  var copyLinkBtn = document.getElementById('copy-link-btn');
  copyLinkBtn && copyLinkBtn.addEventListener('click', function () {
    var url = window.location.href;
    function flashCopied() {
      sharePopover.classList.add('copied');
      setTimeout(function () { sharePopover.classList.remove('copied'); }, 1600);
    }
    if (navigator.clipboard && navigator.clipboard.writeText) {
      navigator.clipboard.writeText(url).then(flashCopied);
    } else {
      var tmp = document.createElement('input');
      document.body.appendChild(tmp);
      tmp.value = url;
      tmp.select();
      document.execCommand('copy');
      document.body.removeChild(tmp);
      flashCopied();
    }
  });

  /* ---------------- Product detail accordion ---------------- */
  document.querySelectorAll('.pdp-acc-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var panel = btn.nextElementSibling;
      var icon = btn.querySelector('.acc-icon');
      panel.classList.toggle('hidden');
      icon && icon.classList.toggle('fa-chevron-up');
      icon && icon.classList.toggle('fa-chevron-down');
    });
  });

});
