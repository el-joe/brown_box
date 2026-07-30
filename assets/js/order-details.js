/* ellamart+ — order details page (reads ?order= from the URL, cancel-order modal) */

document.addEventListener('DOMContentLoaded', function () {

  var params = new URLSearchParams(window.location.search);
  var orderParam = params.get('order');

  if (orderParam) {
    var display = orderParam.indexOf('ELM-') === 0 || orderParam.indexOf('#') === 0
      ? orderParam.replace('#', '')
      : 'ELM-' + orderParam;
    document.querySelectorAll('.js-order-number').forEach(function (el) {
      el.textContent = '#' + display.replace(/^#/, '');
    });
  }

  /* ---------------- Cancel order modal ---------------- */
  var cancelBtn = document.getElementById('cancel-order-btn');
  var cancelModal = document.getElementById('cancel-modal-overlay');
  cancelBtn && cancelBtn.addEventListener('click', function () {
    cancelModal.classList.add('open');
    document.body.classList.add('overflow-hidden');
  });

  var cancelConfirm = document.getElementById('cancel-confirm-btn');
  cancelConfirm && cancelConfirm.addEventListener('click', function () {
    cancelConfirm.disabled = true;
    cancelConfirm.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Cancelling...</span>';
    setTimeout(function () {
      window.location.href = 'account-orders.html';
    }, 900);
  });

  /* ---------------- Copy tracking number ---------------- */
  document.querySelectorAll('.copy-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var value = btn.dataset.copy;
      if (navigator.clipboard) navigator.clipboard.writeText(value).catch(function () {});
      var icon = btn.querySelector('i');
      btn.classList.add('copied');
      icon.classList.remove('fa-copy');
      icon.classList.add('fa-check');
      setTimeout(function () {
        btn.classList.remove('copied');
        icon.classList.remove('fa-check');
        icon.classList.add('fa-copy');
      }, 1500);
    });
  });

});
