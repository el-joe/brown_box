/* ellamart+ — track order page (lookup form + demo timeline reveal) */

document.addEventListener('DOMContentLoaded', function () {

  var params = new URLSearchParams(window.location.search);
  var orderInput = document.getElementById('track-order-number');
  var emailInput = document.getElementById('track-email');
  var form = document.getElementById('track-form');
  var errorEl = document.getElementById('track-error');
  var result = document.getElementById('track-result');
  var resultOrderNumber = document.getElementById('result-order-number');

  if (params.get('order')) {
    orderInput.value = params.get('order').replace(/^#/, '').replace(/^ELM-/, 'ELM-');
    if (orderInput.value.indexOf('ELM-') !== 0) orderInput.value = 'ELM-' + orderInput.value;
  }

  function showResult(orderNumber) {
    if (resultOrderNumber) resultOrderNumber.textContent = '#' + orderNumber.replace(/^#/, '');
    result.classList.remove('hidden');
    setTimeout(function () { result.classList.add('show'); }, 20);
    result.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var orderVal = orderInput.value.trim();
    var emailVal = emailInput.value.trim();

    if (!orderVal || !emailVal) {
      errorEl.classList.remove('hidden');
      orderInput.classList.toggle('input-invalid', !orderVal);
      emailInput.classList.toggle('input-invalid', !emailVal);
      return;
    }
    errorEl.classList.add('hidden');
    orderInput.classList.remove('input-invalid');
    emailInput.classList.remove('input-invalid');

    var btn = form.querySelector('button[type="submit"]');
    var original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    setTimeout(function () {
      btn.disabled = false;
      btn.innerHTML = original;
      showResult(orderVal);
    }, 700);
  });

  /* Auto-run demo lookup if the order number arrived via query string */
  if (params.get('order')) {
    setTimeout(function () { form.requestSubmit ? form.requestSubmit() : form.dispatchEvent(new Event('submit', { cancelable: true })); }, 300);
  }

});
