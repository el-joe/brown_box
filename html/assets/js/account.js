/* ellamart+ — customer account scripts (orders, wishlist, profile, addresses, logout) */

document.addEventListener('DOMContentLoaded', function () {

  /* ---------------- Toast helper ---------------- */
  var toast = document.getElementById('account-toast');
  var toastTimer = null;
  function showToast(message) {
    if (!toast) return;
    toast.querySelector('span').textContent = message;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { toast.classList.remove('show'); }, 3000);
  }

  /* ---------------- Generic modal helpers ---------------- */
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
  document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      closeModal(document.getElementById(btn.dataset.modalClose));
    });
  });

  /* ---------------- Logout ---------------- */
  var logoutBtn = document.getElementById('logout-btn');
  var logoutModal = document.getElementById('logout-modal-overlay');
  logoutBtn && logoutBtn.addEventListener('click', function () {
    openModal(logoutModal);
  });
  var logoutConfirm = document.getElementById('logout-confirm-btn');
  logoutConfirm && logoutConfirm.addEventListener('click', function () {
    logoutConfirm.disabled = true;
    logoutConfirm.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Logging out...</span>';
    setTimeout(function () { window.location.href = 'login.html'; }, 700);
  });

  /* ---------------- Password show/hide toggle (profile page) ---------------- */
  document.querySelectorAll('.toggle-password').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.dataset.target);
      if (!input) return;
      var isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      btn.querySelector('i').classList.toggle('fa-eye', !isHidden);
      btn.querySelector('i').classList.toggle('fa-eye-slash', isHidden);
    });
  });

  /* ================= ORDERS: status tabs ================= */
  var orderTabs = document.querySelectorAll('.order-tab');
  var orderCards = document.querySelectorAll('.order-card');
  var ordersEmpty = document.getElementById('orders-empty');
  if (orderTabs.length) {
    orderTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        orderTabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');
        var filter = tab.dataset.filter;
        var visibleCount = 0;
        orderCards.forEach(function (card) {
          var match = filter === 'all' || card.dataset.status === filter;
          card.classList.toggle('hidden', !match);
          if (match) visibleCount++;
        });
        if (ordersEmpty) ordersEmpty.classList.toggle('hidden', visibleCount !== 0);
      });
    });
  }

  /* ================= WISHLIST: remove item ================= */
  var wishlistGrid = document.getElementById('wishlist-grid');
  var wishlistEmpty = document.getElementById('wishlist-empty');
  function refreshWishlistEmpty() {
    if (!wishlistGrid || !wishlistEmpty) return;
    var remaining = wishlistGrid.querySelectorAll('.wishlist-item').length;
    wishlistEmpty.classList.toggle('hidden', remaining !== 0);
    wishlistGrid.classList.toggle('hidden', remaining === 0);
  }
  document.querySelectorAll('.wishlist-remove').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var item = btn.closest('.wishlist-item');
      if (!item) return;
      item.classList.add('removing');
      setTimeout(function () {
        item.remove();
        refreshWishlistEmpty();
        showToast('Removed from wishlist.');
      }, 250);
    });
  });

  /* ================= EDIT PROFILE ================= */
  var avatarInput = document.getElementById('avatar-input');
  var avatarPreview = document.getElementById('avatar-preview');
  avatarInput && avatarInput.addEventListener('change', function () {
    var file = avatarInput.files && avatarInput.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function (e) { avatarPreview.src = e.target.result; };
    reader.readAsDataURL(file);
  });

  function setError(input, errorEl, message) {
    input.classList.add('input-invalid');
    if (errorEl) { errorEl.textContent = message; errorEl.classList.remove('hidden'); }
  }
  function clearError(input, errorEl) {
    input.classList.remove('input-invalid');
    if (errorEl) errorEl.classList.add('hidden');
  }
  document.querySelectorAll('.account-field input').forEach(function (input) {
    input.addEventListener('input', function () {
      clearError(input, document.getElementById(input.id + '-error'));
    });
  });
  var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  var profileForm = document.getElementById('profile-form');
  profileForm && profileForm.addEventListener('submit', function (e) {
    e.preventDefault();
    var name = document.getElementById('profile-name');
    var email = document.getElementById('profile-email');
    var valid = true;

    if (name.value.trim().length < 2) {
      setError(name, document.getElementById('profile-name-error'), 'Please enter your full name.');
      valid = false;
    } else {
      clearError(name, document.getElementById('profile-name-error'));
    }

    if (!emailRe.test(email.value.trim())) {
      setError(email, document.getElementById('profile-email-error'), 'Please enter a valid email address.');
      valid = false;
    } else {
      clearError(email, document.getElementById('profile-email-error'));
    }

    if (!valid) return;

    var btn = document.getElementById('profile-save-btn');
    var original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Saving...</span>';
    setTimeout(function () {
      btn.disabled = false;
      btn.innerHTML = original;
      showToast('Profile updated successfully.');
    }, 800);
  });

  var passwordForm = document.getElementById('password-form');
  passwordForm && passwordForm.addEventListener('submit', function (e) {
    e.preventDefault();
    var current = document.getElementById('current-password');
    var next = document.getElementById('new-password');
    var confirm = document.getElementById('confirm-password');
    var valid = true;

    if (!current.value) {
      setError(current, document.getElementById('current-password-error'), 'Please enter your current password.');
      valid = false;
    } else {
      clearError(current, document.getElementById('current-password-error'));
    }

    if (next.value.length < 8) {
      setError(next, document.getElementById('new-password-error'), 'New password must be at least 8 characters.');
      valid = false;
    } else {
      clearError(next, document.getElementById('new-password-error'));
    }

    if (confirm.value !== next.value || !confirm.value) {
      setError(confirm, document.getElementById('confirm-password-error'), 'Passwords do not match.');
      valid = false;
    } else {
      clearError(confirm, document.getElementById('confirm-password-error'));
    }

    if (!valid) return;

    var btn = document.getElementById('password-save-btn');
    var original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Updating...</span>';
    setTimeout(function () {
      btn.disabled = false;
      btn.innerHTML = original;
      passwordForm.reset();
      showToast('Password changed successfully.');
    }, 800);
  });

  /* ================= ADDRESSES ================= */
  var addressList = document.getElementById('address-list');
  if (addressList) {
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
    var addressSeq = addressList.querySelectorAll('.address-card').length;

    function resetAddressForm() {
      [afName, afPhone, afStreet, afCity, afArea, afPostal].forEach(function (input) { input.value = ''; });
      afDefault.checked = false;
      addressFormError.classList.add('hidden');
    }

    function clearDefaults() {
      addressList.querySelectorAll('.address-card').forEach(function (card) {
        card.classList.remove('is-default');
        var badge = card.querySelector('.address-badge');
        if (badge) badge.remove();
        var setDefaultBtn = card.querySelector('.address-set-default');
        if (setDefaultBtn) setDefaultBtn.classList.remove('hidden');
      });
    }

    function buildAddressCard(data) {
      var card = document.createElement('div');
      card.className = 'select-card address-card' + (data.isDefault ? ' is-default' : '');
      card.dataset.name = data.name;
      card.dataset.phone = data.phone;
      card.dataset.street = data.street;
      card.dataset.city = data.city;
      card.dataset.area = data.area;
      card.dataset.postal = data.postal;
      card.innerHTML =
        (data.isDefault ? '<span class="address-badge">Default</span>' : '') +
        '<p class="address-name"></p>' +
        '<p class="address-phone"></p>' +
        '<p class="address-text"></p>' +
        '<div class="address-actions">' +
          '<button type="button" class="address-action-btn edit-address-btn"><i class="fa-solid fa-pen"></i> Edit</button>' +
          '<button type="button" class="address-action-btn danger delete-address-btn"><i class="fa-solid fa-trash"></i> Delete</button>' +
          '<button type="button" class="address-set-default' + (data.isDefault ? ' hidden' : '') + '">Set as default</button>' +
        '</div>';
      card.querySelector('.address-name').textContent = data.name;
      card.querySelector('.address-phone').textContent = data.phone;
      card.querySelector('.address-text').textContent = data.street + ', ' + data.area + ', ' + data.city + (data.postal ? ', ' + data.postal : '');
      return card;
    }

    document.getElementById('add-address-btn').addEventListener('click', function () {
      editingCard = null;
      addressModalTitle.textContent = 'Add New Address';
      resetAddressForm();
      openModal(addressModalOverlay);
      setTimeout(function () { afName.focus(); }, 150);
    });

    addressList.addEventListener('click', function (e) {
      var editBtn = e.target.closest('.edit-address-btn');
      var deleteBtn = e.target.closest('.delete-address-btn');
      var defaultBtn = e.target.closest('.address-set-default');

      if (editBtn) {
        var card = editBtn.closest('.address-card');
        editingCard = card;
        addressModalTitle.textContent = 'Edit Address';
        afName.value = card.dataset.name;
        afPhone.value = card.dataset.phone;
        afStreet.value = card.dataset.street;
        afCity.value = card.dataset.city;
        afArea.value = card.dataset.area;
        afPostal.value = card.dataset.postal || '';
        afDefault.checked = card.classList.contains('is-default');
        addressFormError.classList.add('hidden');
        openModal(addressModalOverlay);
      }

      if (deleteBtn) {
        var deleteCard = deleteBtn.closest('.address-card');
        var wasDefault = deleteCard.classList.contains('is-default');
        deleteCard.remove();
        if (wasDefault) {
          var firstRemaining = addressList.querySelector('.address-card');
          if (firstRemaining) {
            firstRemaining.classList.add('is-default');
            if (!firstRemaining.querySelector('.address-badge')) {
              firstRemaining.insertAdjacentHTML('afterbegin', '<span class="address-badge">Default</span>');
            }
            var btn = firstRemaining.querySelector('.address-set-default');
            if (btn) btn.classList.add('hidden');
          }
        }
        showToast('Address removed.');
      }

      if (defaultBtn) {
        var targetCard = defaultBtn.closest('.address-card');
        clearDefaults();
        targetCard.classList.add('is-default');
        targetCard.insertAdjacentHTML('afterbegin', '<span class="address-badge">Default</span>');
        defaultBtn.classList.add('hidden');
        showToast('Default address updated.');
      }
    });

    document.getElementById('address-modal-save').addEventListener('click', function () {
      if (!afName.value.trim() || !afPhone.value.trim() || !afStreet.value.trim() || !afCity.value.trim()) {
        addressFormError.classList.remove('hidden');
        return;
      }
      addressFormError.classList.add('hidden');

      var data = {
        name: afName.value.trim(),
        phone: afPhone.value.trim(),
        street: afStreet.value.trim(),
        city: afCity.value.trim(),
        area: afArea.value.trim(),
        postal: afPostal.value.trim(),
        isDefault: afDefault.checked,
      };

      if (data.isDefault) clearDefaults();

      if (editingCard) {
        editingCard.dataset.name = data.name;
        editingCard.dataset.phone = data.phone;
        editingCard.dataset.street = data.street;
        editingCard.dataset.city = data.city;
        editingCard.dataset.area = data.area;
        editingCard.dataset.postal = data.postal;
        editingCard.querySelector('.address-name').textContent = data.name;
        editingCard.querySelector('.address-phone').textContent = data.phone;
        editingCard.querySelector('.address-text').textContent = data.street + ', ' + data.area + ', ' + data.city + (data.postal ? ', ' + data.postal : '');
        if (data.isDefault) {
          editingCard.classList.add('is-default');
          if (!editingCard.querySelector('.address-badge')) editingCard.insertAdjacentHTML('afterbegin', '<span class="address-badge">Default</span>');
          var sdBtn = editingCard.querySelector('.address-set-default');
          if (sdBtn) sdBtn.classList.add('hidden');
        }
        showToast('Address updated.');
      } else {
        addressSeq++;
        var newCard = buildAddressCard(data);
        var addBtn = document.getElementById('add-address-btn');
        addressList.insertBefore(newCard, addBtn);
        showToast('New address added.');
      }

      closeModal(addressModalOverlay);
    });
  }

});
