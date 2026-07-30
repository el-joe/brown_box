/* ellamart+ — checkout page scripts (address book, payment method, invoice upload, validation, order confirmation) */

document.addEventListener('DOMContentLoaded', function () {

  /* ================= Generic modal helpers ================= */
  function openModal(overlay) {
    overlay.classList.add('open');
    document.body.classList.add('overflow-hidden');
  }
  function closeModal(overlay) {
    overlay.classList.remove('open');
    document.body.classList.remove('overflow-hidden');
  }
  document.querySelectorAll('.modal-overlay').forEach(function (overlay) {
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) closeModal(overlay);
    });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal-overlay.open').forEach(closeModal);
    }
  });

  /* ================= Address selection ================= */
  var addressList = document.getElementById('address-list');

  function refreshAddressChecked() {
    addressList.querySelectorAll('.address-card').forEach(function (card) {
      var radio = card.querySelector('input[type="radio"]');
      card.classList.toggle('checked', radio.checked);
    });
  }
  addressList.addEventListener('change', function (e) {
    if (e.target.matches('input[name="delivery-address"]')) refreshAddressChecked();
  });

  /* ================= Address add / edit modal ================= */
  var addressModalOverlay = document.getElementById('address-modal-overlay');
  var addressModalTitle = document.getElementById('address-modal-title');
  var addressFormError = document.getElementById('address-form-error');
  var afName = document.getElementById('af-name');
  var afPhone = document.getElementById('af-phone');
  var afStreet = document.getElementById('af-street');
  var afCity = document.getElementById('af-city');
  var afArea = document.getElementById('af-area');
  var afPostal = document.getElementById('af-postal');
  var afDefault = document.getElementById('af-default');
  var editingCard = null;
  var addressSeq = 2;

  function resetAddressForm() {
    [afName, afPhone, afStreet, afCity, afArea, afPostal].forEach(function (input) { input.value = ''; });
    afDefault.checked = false;
    addressFormError.classList.add('hidden');
  }

  document.getElementById('add-address-btn').addEventListener('click', function () {
    editingCard = null;
    addressModalTitle.textContent = 'Add New Address';
    resetAddressForm();
    openModal(addressModalOverlay);
  });

  addressList.addEventListener('click', function (e) {
    var editBtn = e.target.closest('.address-edit-btn');
    if (!editBtn) return;
    e.preventDefault();
    var card = editBtn.closest('.address-card');
    editingCard = card;
    addressModalTitle.textContent = 'Edit Address';
    afName.value = card.dataset.name || '';
    afPhone.value = card.dataset.phone || '';
    afStreet.value = card.dataset.street || '';
    afCity.value = card.dataset.city || '';
    afArea.value = card.dataset.area || '';
    afPostal.value = card.dataset.postal || '';
    afDefault.checked = card.dataset.default === 'true';
    addressFormError.classList.add('hidden');
    openModal(addressModalOverlay);
  });

  document.getElementById('address-modal-close').addEventListener('click', function () { closeModal(addressModalOverlay); });
  document.getElementById('address-modal-cancel').addEventListener('click', function () { closeModal(addressModalOverlay); });

  function buildAddressCardHTML(id, data) {
    var badge = data.isDefault ? '<span class="address-badge">Default</span>' : '';
    return (
      '<input type="radio" name="delivery-address" value="' + id + '">' +
      '<span class="select-check"><i class="fa-solid fa-check"></i></span>' +
      badge +
      '<p class="address-name"></p>' +
      '<p class="address-phone"></p>' +
      '<p class="address-text"></p>' +
      '<button type="button" class="address-edit-btn"><i class="fa-solid fa-pen"></i> Edit</button>'
    );
  }

  function applyAddressData(card, data) {
    card.dataset.name = data.name;
    card.dataset.phone = data.phone;
    card.dataset.street = data.street;
    card.dataset.city = data.city;
    card.dataset.area = data.area;
    card.dataset.postal = data.postal;
    card.dataset.default = data.isDefault ? 'true' : 'false';
    card.querySelector('.address-name').textContent = data.name;
    card.querySelector('.address-phone').textContent = data.phone;
    var addressParts = [data.street, data.area, data.city, data.postal, 'Egypt'].filter(Boolean);
    card.querySelector('.address-text').textContent = addressParts.join(', ');

    var existingBadge = card.querySelector('.address-badge');
    if (data.isDefault && !existingBadge) {
      var badge = document.createElement('span');
      badge.className = 'address-badge';
      badge.textContent = 'Default';
      card.insertBefore(badge, card.querySelector('.address-name'));
    } else if (!data.isDefault && existingBadge) {
      existingBadge.remove();
    }
  }

  document.getElementById('address-modal-save').addEventListener('click', function () {
    var data = {
      name: afName.value.trim(),
      phone: afPhone.value.trim(),
      street: afStreet.value.trim(),
      city: afCity.value.trim(),
      area: afArea.value.trim(),
      postal: afPostal.value.trim(),
      isDefault: afDefault.checked,
    };
    if (!data.name || !data.phone || !data.street || !data.city) {
      addressFormError.classList.remove('hidden');
      return;
    }
    addressFormError.classList.add('hidden');

    if (data.isDefault) {
      addressList.querySelectorAll('.address-card').forEach(function (c) {
        var badge = c.querySelector('.address-badge');
        if (badge) badge.remove();
        c.dataset.default = 'false';
      });
    }

    if (editingCard) {
      applyAddressData(editingCard, data);
      if (data.isDefault) editingCard.querySelector('input[type="radio"]').checked = true;
    } else {
      addressSeq += 1;
      var id = 'addr-' + addressSeq;
      var card = document.createElement('label');
      card.className = 'select-card address-card';
      card.dataset.id = id;
      card.innerHTML = buildAddressCardHTML(id, data);
      applyAddressData(card, data);
      var addTile = document.getElementById('add-address-btn');
      addressList.insertBefore(card, addTile);
      if (data.isDefault) card.querySelector('input[type="radio"]').checked = true;
    }

    refreshAddressChecked();
    closeModal(addressModalOverlay);
  });

  /* ================= Payment method selection ================= */
  var paymentMethods = document.getElementById('payment-methods');
  var paymentDetails = document.getElementById('payment-details');
  var paymentError = document.getElementById('payment-error');
  var pmPanels = {
    bank: document.getElementById('pm-details-bank'),
    vodafone: document.getElementById('pm-details-vodafone'),
    instapay: document.getElementById('pm-details-instapay'),
  };

  paymentMethods.addEventListener('change', function (e) {
    if (!e.target.matches('input[name="payment-method"]')) return;

    paymentMethods.querySelectorAll('.payment-card').forEach(function (card) {
      card.classList.toggle('checked', card.querySelector('input').checked);
    });

    var method = e.target.closest('.payment-card').dataset.method;
    paymentDetails.classList.remove('hidden');
    paymentError.classList.add('hidden');
    Object.keys(pmPanels).forEach(function (key) {
      pmPanels[key].classList.toggle('hidden', key !== method);
    });
  });

  /* ================= Copy-to-clipboard (bank/wallet numbers) ================= */
  document.querySelectorAll('.copy-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var text = btn.dataset.copy || '';
      function flash() {
        var icon = btn.querySelector('i');
        btn.classList.add('copied');
        icon.className = 'fa-solid fa-check';
        setTimeout(function () {
          btn.classList.remove('copied');
          icon.className = 'fa-regular fa-copy';
        }, 1400);
      }
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(flash);
      } else {
        var tmp = document.createElement('input');
        document.body.appendChild(tmp);
        tmp.value = text;
        tmp.select();
        document.execCommand('copy');
        document.body.removeChild(tmp);
        flash();
      }
    });
  });

  /* ================= Invoice file upload ================= */
  var dropzone = document.getElementById('file-dropzone');
  var fileInput = document.getElementById('invoice-input');
  var filePreview = document.getElementById('file-preview');
  var filePreviewName = document.getElementById('file-preview-name');
  var filePreviewSize = document.getElementById('file-preview-size');
  var fileRemoveBtn = document.getElementById('file-remove-btn');
  var invoiceError = document.getElementById('invoice-error');

  function formatBytes(bytes) {
    if (bytes < 1024) return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
  }

  function showFile(file) {
    filePreviewName.textContent = file.name;
    filePreviewSize.textContent = formatBytes(file.size);
    filePreview.classList.remove('hidden');
    dropzone.classList.add('hidden');
    invoiceError.classList.add('hidden');
  }

  function clearFile() {
    fileInput.value = '';
    filePreview.classList.add('hidden');
    dropzone.classList.remove('hidden');
  }

  dropzone.addEventListener('click', function () { fileInput.click(); });
  fileInput.addEventListener('change', function () {
    if (fileInput.files && fileInput.files[0]) showFile(fileInput.files[0]);
  });
  fileRemoveBtn.addEventListener('click', function (e) {
    e.stopPropagation();
    clearFile();
  });

  ['dragenter', 'dragover'].forEach(function (evt) {
    dropzone.addEventListener(evt, function (e) {
      e.preventDefault();
      dropzone.classList.add('dragover');
    });
  });
  ['dragleave', 'drop'].forEach(function (evt) {
    dropzone.addEventListener(evt, function (e) {
      e.preventDefault();
      dropzone.classList.remove('dragover');
    });
  });
  dropzone.addEventListener('drop', function (e) {
    var file = e.dataTransfer.files && e.dataTransfer.files[0];
    if (file) {
      fileInput.files = e.dataTransfer.files;
      showFile(file);
    }
  });

  /* ================= Place order ================= */
  var successModalOverlay = document.getElementById('success-modal-overlay');
  var successOrderNumber = document.getElementById('success-order-number');

  document.getElementById('place-order-btn').addEventListener('click', function () {
    var methodSelected = paymentMethods.querySelector('input[name="payment-method"]:checked');
    var hasFile = fileInput.files && fileInput.files.length > 0;
    var valid = true;

    if (!methodSelected) {
      paymentError.classList.remove('hidden');
      valid = false;
    } else {
      paymentError.classList.add('hidden');
    }

    if (methodSelected && !hasFile) {
      invoiceError.classList.remove('hidden');
      valid = false;
    } else if (hasFile) {
      invoiceError.classList.add('hidden');
    }

    if (!valid) {
      var firstError = document.querySelector('#payment-error:not(.hidden), #invoice-error:not(.hidden)');
      firstError && firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }

    var orderNumber = '#ELM-' + Math.floor(100000 + Math.random() * 900000);
    successOrderNumber.textContent = orderNumber;
    var trackLink = document.getElementById('success-track-link');
    if (trackLink) trackLink.href = 'track-order.html?order=' + encodeURIComponent(orderNumber.replace('#', ''));
    openModal(successModalOverlay);
  });

  /* ================= Recommended / Best Sellers swipers (arrows only) ================= */
  function initArrowSwiper(selector, breakpoints) {
    if (!window.Swiper || !document.querySelector(selector)) return;
    new Swiper(selector, {
      slidesPerView: 2,
      spaceBetween: 16,
      navigation: {
        nextEl: selector + ' .swiper-button-next',
        prevEl: selector + ' .swiper-button-prev',
      },
      breakpoints: breakpoints,
    });
  }
  var bp = {
    480: { slidesPerView: 2 },
    768: { slidesPerView: 3 },
    1024: { slidesPerView: 4 },
    1280: { slidesPerView: 6 },
  };
  initArrowSwiper('.recommended-checkout-swiper', bp);
  initArrowSwiper('.bestsellers-swiper', bp);

});
