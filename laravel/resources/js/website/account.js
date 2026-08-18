function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

function showToast(message, isError = false) {
    const toast = document.getElementById('web-account-toast');
    if (!toast) return;

    toast.querySelector('span').textContent = message;
    toast.classList.toggle('is-error', isError);
    toast.classList.add('show');
    clearTimeout(toast._timer);
    toast._timer = setTimeout(() => toast.classList.remove('show'), 3200);
}

function clearFieldErrors(form) {
    form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
    form.querySelectorAll('.web-field-error').forEach((el) => el.classList.add('hidden'));
}

function applyFieldErrors(form, errors) {
    Object.keys(errors).forEach((field) => {
        const input = form.querySelector(`[name="${field}"]`);
        if (input) input.classList.add('is-invalid');

        const errorEl = form.querySelector(`#${form.id}-${field}-error`) || document.getElementById(field + '-error');
        if (errorEl) {
            errorEl.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> ' + errors[field][0];
            errorEl.classList.remove('hidden');
        }
    });
}

async function ajaxSubmitForm(form, { method = null, onSuccessReload = true, onSuccess = null } = {}) {
    const formData = new FormData(form);
    const params = new URLSearchParams();
    formData.forEach((value, key) => params.append(key, value));
    if (method) params.set('_method', method);

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
        clearFieldErrors(form);
        applyFieldErrors(form, data.errors || {});
        showToast(data.message || 'Please check the form for errors.', true);
        return false;
    }

    if (response.ok || response.redirected) {
        if (onSuccess) {
            onSuccess();
        } else if (onSuccessReload) {
            window.location.reload();
        }
        return true;
    }

    showToast('Something went wrong. Please try again.', true);
    return false;
}

document.addEventListener('DOMContentLoaded', function () {
    /* ---------------- Password show/hide toggle ---------------- */
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

    /* ---------------- Profile form ---------------- */
    const profileForm = document.getElementById('profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('profile-submit-btn');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            await ajaxSubmitForm(profileForm, {
                method: 'PUT',
                onSuccess: () => showToast('Profile updated successfully.'),
            });

            btn.disabled = false;
            btn.innerHTML = original;
        });
    }

    /* ---------------- Password change form ---------------- */
    const passwordForm = document.getElementById('password-form');
    if (passwordForm) {
        passwordForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const newPassword = document.getElementById('new-password');
            const confirmPassword = document.getElementById('confirm-password');

            if (newPassword.value && newPassword.value !== confirmPassword.value) {
                showToast('Passwords do not match.', true);
                return;
            }

            const btn = document.getElementById('password-submit-btn');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            await ajaxSubmitForm(passwordForm, {
                method: 'PUT',
                onSuccess: () => {
                    showToast('Password updated successfully.');
                    passwordForm.reset();
                },
            });

            btn.disabled = false;
            btn.innerHTML = original;
        });
    }

    /* ---------------- Refund request form ---------------- */
    const refundForm = document.getElementById('refund-form');
    if (refundForm) {
        refundForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const btn = document.getElementById('refund-submit-btn');
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

            await ajaxSubmitForm(refundForm, {
                onSuccess: () => window.location.reload(),
            });

            btn.disabled = false;
            btn.innerHTML = original;
        });
    }

    /* ---------------- Address delete forms ---------------- */
    document.querySelectorAll('.address-delete-form').forEach(function (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            if (!confirm('Remove this address?')) return;

            await ajaxSubmitForm(form, { method: 'DELETE' });
        });
    });

});

/**
 * Shared Alpine.js component powering the create/edit address modals on both
 * the account "My Addresses" page and the checkout page.
 *
 * @param {{storeUrl: string, updateUrlBase?: string, governorates: Array, citiesByGovernorate: Object}} config
 */
function addressManager(config) {
    return {
        open: false,
        mode: 'create',
        saving: false,
        errors: {},
        governorates: config.governorates || [],
        citiesByGovernorate: config.citiesByGovernorate || {},
        storeUrl: config.storeUrl,
        updateUrlBase: config.updateUrlBase || config.storeUrl,
        form: {
            id: null,
            label: '',
            name: '',
            phone: '',
            governorate_id: '',
            city_id: '',
            address_line: '',
            is_default: false,
        },

        get availableCities() {
            return this.citiesByGovernorate[this.form.governorate_id] || [];
        },

        onGovernorateChange() {
            this.form.city_id = '';
        },

        resetForm() {
            this.form = {
                id: null,
                label: '',
                name: '',
                phone: '',
                governorate_id: '',
                city_id: '',
                address_line: '',
                is_default: false,
            };
            this.errors = {};
        },

        openCreate() {
            this.resetForm();
            this.mode = 'create';
            this.open = true;
        },

        openEdit(address) {
            this.errors = {};
            this.mode = 'edit';
            this.form = {
                id: address.id,
                label: address.label || '',
                name: address.name || '',
                phone: address.phone || '',
                governorate_id: address.governorate_id ?? '',
                city_id: address.city_id ?? '',
                address_line: address.address_line || '',
                is_default: !!address.is_default,
            };
            this.open = true;
        },

        close() {
            this.open = false;
        },

        async submit() {
            this.saving = true;
            this.errors = {};

            const isEdit = this.mode === 'edit';
            const url = isEdit ? `${this.updateUrlBase}/${this.form.id}` : this.storeUrl;
            const params = new URLSearchParams();

            params.append('label', this.form.label || '');
            params.append('name', this.form.name || '');
            params.append('phone', this.form.phone || '');
            params.append('governorate_id', this.form.governorate_id || '');
            params.append('city_id', this.form.city_id || '');
            params.append('address_line', this.form.address_line || '');
            params.append('is_default', this.form.is_default ? '1' : '0');
            if (isEdit) params.append('_method', 'PUT');

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken(),
                        Accept: 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: params.toString(),
                });

                const data = await response.json();

                if (!response.ok || !data.success) {
                    this.errors = data.errors || {};
                    window.toastr?.error(data.message || 'Please check the form for errors.');
                    this.saving = false;
                    return;
                }

                window.toastr?.success(data.message || 'Address saved successfully.');
                this.saving = false;
                this.open = false;
                window.location.reload();
            } catch (err) {
                this.saving = false;
                window.toastr?.error('Something went wrong. Please try again.');
            }
        },
    };
}

window.addressManager = addressManager;
