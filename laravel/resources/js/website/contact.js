function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content ?? '';
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('contact-form');
    if (!form) return;

    const submitBtn = document.getElementById('contact-submit-btn');
    const successEl = document.getElementById('contact-success');

    function clearErrors() {
        form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
        form.querySelectorAll('.web-field-error').forEach((el) => el.classList.add('hidden'));
    }

    function showFieldError(field, message) {
        const input = form.querySelector(`[name="${field}"]`);
        input?.classList.add('is-invalid');
        const errorEl = document.getElementById(`contact-${field}-error`);
        if (errorEl) {
            if (message) errorEl.innerHTML = `<i class="fa-solid fa-circle-exclamation"></i> ${message}`;
            errorEl.classList.remove('hidden');
        }
    }

    function validateClientSide() {
        clearErrors();
        let valid = true;

        const name = form.querySelector('#contact-name').value.trim();
        const email = form.querySelector('#contact-email').value.trim();
        const subject = form.querySelector('#contact-subject').value;
        const message = form.querySelector('#contact-message').value.trim();

        if (!name) {
            showFieldError('name');
            valid = false;
        }

        if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showFieldError('email');
            valid = false;
        }

        if (!subject) {
            showFieldError('subject');
            valid = false;
        }

        if (!message || message.length < 10) {
            showFieldError('message');
            valid = false;
        }

        return valid;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        successEl?.classList.add('hidden');

        if (!validateClientSide()) return;

        const originalHtml = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken(),
                    Accept: 'application/json',
                },
                body: formData,
            });

            if (response.status === 422) {
                const data = await response.json();
                clearErrors();
                Object.keys(data.errors || {}).forEach((field) => {
                    showFieldError(field, data.errors[field][0]);
                });
                return;
            }

            if (response.ok) {
                form.reset();
                successEl?.classList.remove('hidden');
            }
        } catch (err) {
            // Network error — leave the form filled in so the user can retry.
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalHtml;
        }
    });
});
