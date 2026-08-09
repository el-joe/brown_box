// Category / product listing page behaviour: mobile filters panel toggle is handled
// declaratively via Alpine (x-data on the page root) — this file covers anything Alpine
// can't express cleanly, e.g. syncing min/max price inputs.

document.addEventListener('DOMContentLoaded', () => {
    const minPrice = document.querySelector('input[name="min_price"]');
    const maxPrice = document.querySelector('input[name="max_price"]');

    if (minPrice && maxPrice) {
        const clamp = () => {
            const min = parseFloat(minPrice.value);
            const max = parseFloat(maxPrice.value);

            if (!Number.isNaN(min) && !Number.isNaN(max) && min > max) {
                maxPrice.value = minPrice.value;
            }
        };

        minPrice.addEventListener('change', clamp);
        maxPrice.addEventListener('change', clamp);
    }
});
