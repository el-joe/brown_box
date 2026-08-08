/* ellamart+ — shopping cart page scripts (qty, remove, live totals, coupon, related swiper) */

document.addEventListener('DOMContentLoaded', function () {

  var itemsList = document.getElementById('cart-items-list');
  var emptyState = document.getElementById('empty-cart-state');
  var itemsCountEl = document.getElementById('items-count');
  var cartCountEl = document.getElementById('cart-count');
  var checkoutBtn = document.getElementById('checkout-btn');

  var FREE_SHIPPING_THRESHOLD = 50;
  var SHIPPING_FLAT = 6.99;

  var COUPONS = {
    SAVE10: { type: 'percent', value: 10 },
    WELCOME30: { type: 'percent', value: 30 },
  };
  var activeCoupon = null;

  function formatUSD(value) {
    return '$' + value.toFixed(2);
  }

  function getRows() {
    return Array.prototype.slice.call(itemsList.querySelectorAll('.cart-item'));
  }

  function recalc() {
    var rows = getRows();
    var subtotal = 0;
    var totalQty = 0;

    rows.forEach(function (row) {
      var price = Number(row.dataset.price);
      var qtyInput = row.querySelector('.qty-input');
      var qty = Number(qtyInput.value) || 1;
      var lineTotal = price * qty;
      row.querySelector('.cart-line-total').textContent = formatUSD(lineTotal);
      subtotal += lineTotal;
      totalQty += qty;
    });

    var discount = 0;
    if (activeCoupon) {
      discount = activeCoupon.type === 'percent' ? subtotal * (activeCoupon.value / 100) : activeCoupon.value;
    }
    var shipping = rows.length === 0 ? 0 : (subtotal - discount >= FREE_SHIPPING_THRESHOLD ? 0 : SHIPPING_FLAT);
    var total = Math.max(0, subtotal - discount + shipping);

    document.getElementById('summary-subtotal').textContent = formatUSD(subtotal);
    document.getElementById('summary-shipping').textContent = shipping === 0 ? 'Free' : formatUSD(shipping);
    document.getElementById('summary-total').textContent = formatUSD(total);

    var discountRow = document.getElementById('summary-discount-row');
    if (activeCoupon && discount > 0) {
      discountRow.classList.remove('hidden');
      document.getElementById('summary-discount-label').textContent = '(' + activeCoupon.code + ')';
      document.getElementById('summary-discount').textContent = '−' + formatUSD(discount);
    } else {
      discountRow.classList.add('hidden');
    }

    if (itemsCountEl) itemsCountEl.textContent = String(totalQty);
    if (cartCountEl) cartCountEl.textContent = String(totalQty);

    var isEmpty = rows.length === 0;
    emptyState.classList.toggle('hidden', !isEmpty);
    itemsList.classList.toggle('hidden', isEmpty);
    if (checkoutBtn) checkoutBtn.disabled = isEmpty;
  }

  function clampQty(val) {
    val = parseInt(val, 10);
    if (isNaN(val)) val = 1;
    return Math.min(20, Math.max(1, val));
  }

  function bindRow(row) {
    var qtyInput = row.querySelector('.qty-input');
    var minus = row.querySelector('.qty-minus');
    var plus = row.querySelector('.qty-plus');
    var removeBtn = row.querySelector('.cart-item-remove');

    minus.addEventListener('click', function () {
      qtyInput.value = clampQty(Number(qtyInput.value) - 1);
      recalc();
    });
    plus.addEventListener('click', function () {
      qtyInput.value = clampQty(Number(qtyInput.value) + 1);
      recalc();
    });
    qtyInput.addEventListener('change', function () {
      qtyInput.value = clampQty(qtyInput.value);
      recalc();
    });

    removeBtn.addEventListener('click', function () {
      row.classList.add('removing');
      setTimeout(function () {
        row.remove();
        recalc();
      }, 200);
    });
  }

  getRows().forEach(bindRow);
  recalc();

  /* ---------------- Clear cart ---------------- */
  var clearCartBtn = document.getElementById('clear-cart-btn');
  clearCartBtn && clearCartBtn.addEventListener('click', function () {
    getRows().forEach(function (row) { row.remove(); });
    recalc();
  });

  /* ---------------- Coupon ---------------- */
  var couponInput = document.getElementById('coupon-input');
  var couponApplyBtn = document.getElementById('coupon-apply-btn');
  var couponMessage = document.getElementById('coupon-message');

  function showCouponMessage(text, type) {
    couponMessage.textContent = text;
    couponMessage.className = 'coupon-message ' + type;
  }

  couponApplyBtn && couponApplyBtn.addEventListener('click', function () {
    var code = (couponInput.value || '').trim().toUpperCase();
    if (!code) {
      showCouponMessage('Please enter a coupon code.', 'error');
      return;
    }
    var found = COUPONS[code];
    if (found) {
      activeCoupon = { code: code, type: found.type, value: found.value };
      showCouponMessage('"' + code + '" applied — ' + found.value + '% off your subtotal!', 'success');
    } else {
      activeCoupon = null;
      showCouponMessage('"' + code + '" is not a valid or active coupon code.', 'error');
    }
    recalc();
  });

  couponInput && couponInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      couponApplyBtn.click();
    }
  });

  /* ---------------- Proceed to checkout ---------------- */
  checkoutBtn && checkoutBtn.addEventListener('click', function () {
    if (checkoutBtn.disabled) return;
    window.location.href = 'checkout.html';
  });

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

});
