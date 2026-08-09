document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('track-form');
    if (!form) return;

    const errorEl = document.getElementById('track-error');

    form.addEventListener('submit', (e) => {
        const orderNumber = document.getElementById('track-order-number')?.value.trim();
        const phone = document.getElementById('track-phone')?.value.trim();

        if (!orderNumber || !phone) {
            e.preventDefault();
            errorEl?.classList.remove('hidden');
        } else {
            errorEl?.classList.add('hidden');
        }
    });
});
