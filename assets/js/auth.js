/* ellamart+ — login / register page scripts (validation, password toggle, strength meter) */

document.addEventListener('DOMContentLoaded', function () {

  /* ---------------- Toast helper ---------------- */
  var toast = document.getElementById('auth-toast');
  var toastTimer = null;
  function showToast(message) {
    if (!toast) return;
    toast.querySelector('span').textContent = message;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(function () { toast.classList.remove('show'); }, 3200);
  }

  /* ---------------- Password show/hide toggle ---------------- */
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

  /* ---------------- Field error helpers ---------------- */
  function setError(input, errorEl, message) {
    input.classList.add('input-invalid');
    if (errorEl) {
      errorEl.textContent = message;
      errorEl.classList.remove('hidden');
    }
  }
  function clearError(input, errorEl) {
    input.classList.remove('input-invalid');
    if (errorEl) errorEl.classList.add('hidden');
  }
  document.querySelectorAll('.auth-field input').forEach(function (input) {
    input.addEventListener('input', function () {
      clearError(input, document.getElementById(input.id + '-error'));
    });
  });

  var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  /* ================= LOGIN FORM ================= */
  var loginForm = document.getElementById('login-form');
  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var email = document.getElementById('login-email');
      var password = document.getElementById('login-password');
      var valid = true;

      if (!emailRe.test(email.value.trim())) {
        setError(email, document.getElementById('login-email-error'), 'Please enter a valid email address.');
        valid = false;
      } else {
        clearError(email, document.getElementById('login-email-error'));
      }

      if (password.value.length < 6) {
        setError(password, document.getElementById('login-password-error'), 'Password must be at least 6 characters.');
        valid = false;
      } else {
        clearError(password, document.getElementById('login-password-error'));
      }

      if (!valid) return;

      var btn = document.getElementById('login-submit-btn');
      var originalHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Signing in...</span>';

      setTimeout(function () {
        showToast('Welcome back! Redirecting to your account...');
        setTimeout(function () { window.location.href = 'account-orders.html'; }, 1100);
      }, 900);
    });
  }

  /* ================= REGISTER FORM ================= */
  var registerForm = document.getElementById('register-form');
  if (registerForm) {

    /* Password strength meter */
    var pwInput = document.getElementById('register-password');
    var pwBar = document.getElementById('pw-strength-bar');
    var pwLabel = document.getElementById('pw-strength-label');
    var pwLevels = [
      { label: '', color: '#eef0f4', width: '0%' },
      { label: 'Weak', color: '#dc2626', width: '25%' },
      { label: 'Fair', color: '#f59e0b', width: '50%' },
      { label: 'Good', color: '#2b6ee0', width: '75%' },
      { label: 'Strong', color: '#10b981', width: '100%' },
    ];
    function scorePassword(value) {
      var score = 0;
      if (value.length >= 8) score++;
      if (/[A-Z]/.test(value)) score++;
      if (/[0-9]/.test(value)) score++;
      if (/[^A-Za-z0-9]/.test(value)) score++;
      return score;
    }
    pwInput && pwInput.addEventListener('input', function () {
      var score = pwInput.value ? Math.max(1, scorePassword(pwInput.value)) : 0;
      var level = pwLevels[score];
      pwBar.style.width = level.width;
      pwBar.style.background = level.color;
      pwLabel.textContent = pwInput.value ? level.label : '';
    });

    registerForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var name = document.getElementById('register-name');
      var email = document.getElementById('register-email');
      var password = document.getElementById('register-password');
      var confirm = document.getElementById('register-confirm');
      var terms = document.getElementById('register-terms');
      var valid = true;

      if (name.value.trim().length < 2) {
        setError(name, document.getElementById('register-name-error'), 'Please enter your full name.');
        valid = false;
      } else {
        clearError(name, document.getElementById('register-name-error'));
      }

      if (!emailRe.test(email.value.trim())) {
        setError(email, document.getElementById('register-email-error'), 'Please enter a valid email address.');
        valid = false;
      } else {
        clearError(email, document.getElementById('register-email-error'));
      }

      if (password.value.length < 8) {
        setError(password, document.getElementById('register-password-error'), 'Password must be at least 8 characters.');
        valid = false;
      } else {
        clearError(password, document.getElementById('register-password-error'));
      }

      if (confirm.value !== password.value || !confirm.value) {
        setError(confirm, document.getElementById('register-confirm-error'), 'Passwords do not match.');
        valid = false;
      } else {
        clearError(confirm, document.getElementById('register-confirm-error'));
      }

      var termsError = document.getElementById('register-terms-error');
      if (!terms.checked) {
        termsError.classList.remove('hidden');
        valid = false;
      } else {
        termsError.classList.add('hidden');
      }

      if (!valid) return;

      var btn = document.getElementById('register-submit-btn');
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Creating account...</span>';

      setTimeout(function () {
        showToast('Account created! Redirecting to sign in...');
        setTimeout(function () { window.location.href = 'login.html'; }, 1100);
      }, 900);
    });
  }

});
