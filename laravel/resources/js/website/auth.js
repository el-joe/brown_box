function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function showToast(message, isError = false) {
    const toast = document.getElementById('web-auth-toast');
    if (!toast) return;

    toast.querySelector('span').textContent = message;
    toast.classList.toggle('is-error', isError);
    toast.classList.add('show');
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => toast.classList.remove('show'), 3200);
}

function setError(input, errorEl) {
    input.classList.add('is-invalid');
    if (errorEl) errorEl.classList.remove('hidden');
}

function clearError(input, errorEl) {
    input.classList.remove('is-invalid');
    if (errorEl) errorEl.classList.add('hidden');
}

async function ajaxSubmit(form, onFieldErrors) {
    const formData = new FormData(form);
    const params = new URLSearchParams();
    formData.forEach((value, key) => params.append(key, value));

    const response = await fetch(form.action, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken(),
            Accept: 'application/json',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params.toString(),
    });

    if (response.status === 422) {
        const data = await response.json();
        onFieldErrors(data.errors || {});
        return false;
    }

    if (response.ok || response.redirected) {
        window.location.href = response.url || window.location.href;
        return true;
    }

    showToast('Something went wrong. Please try again.', true);
    return false;
}

document.addEventListener('DOMContentLoaded', function () {
    /* Password show/hide toggle */
    document.querySelectorAll('.web-toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById(btn.dataset.target);
            if (!input) return;
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.querySelector('i').classList.toggle('fa-eye', !isHidden);
            btn.querySelector('i').classList.toggle('fa-eye-slash', isHidden);
        });
    });

    document.querySelectorAll('.web-auth-field input').forEach(function (input) {
        input.addEventListener('input', function () {
            clearError(input, document.getElementById(input.id + '-error'));
        });
    });

    const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    /* ================= LOGIN FORM ================= */
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const email = document.getElementById('login-email');
            const password = document.getElementById('login-password');
            let valid = true;

            if (!emailRe.test(email.value.trim())) {
                setError(email, document.getElementById('login-email-error'));
                valid = false;
            } else {
                clearError(email, document.getElementById('login-email-error'));
            }

            if (!password.value) {
                setError(password, document.getElementById('login-password-error'));
                valid = false;
            } else {
                clearError(password, document.getElementById('login-password-error'));
            }

            if (!valid) return;

            const btn = document.getElementById('login-submit-btn');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>' + (window.i18n?.signingIn || 'Signing in...') + '</span>';

            try {
                const ok = await ajaxSubmit(loginForm, function (errors) {
                    Object.keys(errors).forEach(function (field) {
                        const input = document.getElementById('login-' + field);
                        const errorEl = document.getElementById('login-' + field + '-error');
                        if (input) {
                            input.classList.add('is-invalid');
                            if (errorEl) {
                                errorEl.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> ' + errors[field][0];
                                errorEl.classList.remove('hidden');
                            }
                        }
                    });
                });
                if (!ok) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (err) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                showToast('Something went wrong. Please try again.', true);
            }
        });
    }

    /* ================= REGISTER FORM ================= */
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        const pwInput = document.getElementById('register-password');
        const pwBar = document.getElementById('pw-strength-bar');
        const pwLabel = document.getElementById('pw-strength-label');
        const pwLevels = [
            { label: '', color: '#e2e8f0', width: '0%' },
            { label: 'Weak', color: '#dc2626', width: '25%' },
            { label: 'Fair', color: '#f59e0b', width: '50%' },
            { label: 'Good', color: '#2563eb', width: '75%' },
            { label: 'Strong', color: '#10b981', width: '100%' },
        ];

        function scorePassword(value) {
            let score = 0;
            if (value.length >= 8) score++;
            if (/[A-Z]/.test(value)) score++;
            if (/[0-9]/.test(value)) score++;
            if (/[^A-Za-z0-9]/.test(value)) score++;
            return score;
        }

        pwInput && pwInput.addEventListener('input', function () {
            const score = pwInput.value ? Math.max(1, scorePassword(pwInput.value)) : 0;
            const level = pwLevels[score];
            pwBar.style.width = level.width;
            pwBar.style.background = level.color;
            pwLabel.textContent = pwInput.value ? level.label : '';
        });

        registerForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const name = document.getElementById('register-name');
            const email = document.getElementById('register-email');
            const password = document.getElementById('register-password');
            const confirm = document.getElementById('register-confirm');
            const terms = document.getElementById('register-terms');
            let valid = true;

            if (name.value.trim().length < 2) {
                setError(name, document.getElementById('register-name-error'));
                valid = false;
            } else {
                clearError(name, document.getElementById('register-name-error'));
            }

            if (!emailRe.test(email.value.trim())) {
                setError(email, document.getElementById('register-email-error'));
                valid = false;
            } else {
                clearError(email, document.getElementById('register-email-error'));
            }

            if (password.value.length < 8) {
                setError(password, document.getElementById('register-password-error'));
                valid = false;
            } else {
                clearError(password, document.getElementById('register-password-error'));
            }

            if (confirm.value !== password.value || !confirm.value) {
                setError(confirm, document.getElementById('register-confirm-error'));
                valid = false;
            } else {
                clearError(confirm, document.getElementById('register-confirm-error'));
            }

            const termsError = document.getElementById('register-terms-error');
            if (!terms.checked) {
                termsError.classList.remove('hidden');
                valid = false;
            } else {
                termsError.classList.add('hidden');
            }

            if (!valid) return;

            const btn = document.getElementById('register-submit-btn');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Creating account...</span>';

            try {
                const ok = await ajaxSubmit(registerForm, function (errors) {
                    Object.keys(errors).forEach(function (field) {
                        const input = document.getElementById('register-' + (field === 'password_confirmation' ? 'confirm' : field));
                        const errorEl = document.getElementById('register-' + (field === 'password_confirmation' ? 'confirm' : field) + '-error');
                        if (input) {
                            input.classList.add('is-invalid');
                            if (errorEl) {
                                errorEl.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> ' + errors[field][0];
                                errorEl.classList.remove('hidden');
                            }
                        }
                    });
                });
                if (!ok) {
                    btn.disabled = false;
                    btn.innerHTML = originalHtml;
                }
            } catch (err) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                showToast('Something went wrong. Please try again.', true);
            }
        });
    }
});
