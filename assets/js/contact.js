/* ellamart+ — contact page form validation + fake submit */

document.addEventListener('DOMContentLoaded', function () {

  function setError(input, errorEl, message) {
    input.classList.add('input-invalid');
    if (errorEl) { errorEl.textContent = message; errorEl.classList.remove('hidden'); }
  }
  function clearError(input, errorEl) {
    input.classList.remove('input-invalid');
    if (errorEl) errorEl.classList.add('hidden');
  }
  document.querySelectorAll('.contact-field input, .contact-field textarea').forEach(function (input) {
    input.addEventListener('input', function () {
      clearError(input, document.getElementById(input.id + '-error'));
    });
  });

  var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  var form = document.getElementById('contact-form');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var name = document.getElementById('contact-name');
    var email = document.getElementById('contact-email');
    var subject = document.getElementById('contact-subject');
    var message = document.getElementById('contact-message');
    var valid = true;

    if (name.value.trim().length < 2) {
      setError(name, document.getElementById('contact-name-error'), 'Please enter your name.');
      valid = false;
    } else {
      clearError(name, document.getElementById('contact-name-error'));
    }

    if (!emailRe.test(email.value.trim())) {
      setError(email, document.getElementById('contact-email-error'), 'Please enter a valid email address.');
      valid = false;
    } else {
      clearError(email, document.getElementById('contact-email-error'));
    }

    if (!subject.value) {
      setError(subject, document.getElementById('contact-subject-error'), 'Please choose a subject.');
      valid = false;
    } else {
      clearError(subject, document.getElementById('contact-subject-error'));
    }

    if (message.value.trim().length < 10) {
      setError(message, document.getElementById('contact-message-error'), 'Please enter a message of at least 10 characters.');
      valid = false;
    } else {
      clearError(message, document.getElementById('contact-message-error'));
    }

    if (!valid) return;

    var btn = document.getElementById('contact-submit-btn');
    var original = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Sending...</span>';

    setTimeout(function () {
      btn.disabled = false;
      btn.innerHTML = original;
      form.reset();
      document.getElementById('contact-success').classList.remove('hidden');
      document.getElementById('contact-success').scrollIntoView({ behavior: 'smooth', block: 'center' });
    }, 900);
  });

});
