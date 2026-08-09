function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function clearErrors(form) {
    form.querySelectorAll('.admin-field-error').forEach((el) => el.remove());
    form.querySelectorAll('.is-invalid').forEach((el) => el.classList.remove('is-invalid'));
}

function renderErrors(form, errors) {
    clearErrors(form);

    Object.entries(errors || {}).forEach(([field, messages]) => {
        const input = form.querySelector(`[name="${field}"], [name="${field}[]"]`);
        const message = Array.isArray(messages) ? messages[0] : messages;

        if (input) {
            input.classList.add('is-invalid');
            const error = document.createElement('p');
            error.className = 'admin-field-error text-xs text-red-600 mt-1';
            error.textContent = message;
            input.closest('.admin-field')?.appendChild(error) ?? input.insertAdjacentElement('afterend', error);
        }
    });
}

async function post(url, formData) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken(),
            Accept: 'application/json',
        },
        body: formData,
    });

    let data = {};
    try {
        data = await response.json();
    } catch (e) {
        data = {};
    }

    return { ok: response.ok, status: response.status, data };
}

const AdminForm = {
    async submit(formId, validateUrl, submitUrl, onSuccess) {
        const form = document.getElementById(formId);

        if (! form) {
            return;
        }

        clearErrors(form);
        const formData = new FormData(form);

        const validation = await post(validateUrl, formData);

        if (! validation.ok || validation.data.errors) {
            renderErrors(form, validation.data.errors);
            return;
        }

        const result = await post(submitUrl, formData);

        if (! result.ok || result.data.errors) {
            renderErrors(form, result.data.errors);
            return;
        }

        if (typeof onSuccess === 'function') {
            onSuccess(result.data);
        }
    },
};

window.AdminForm = AdminForm;

export default AdminForm;
