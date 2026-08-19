document.addEventListener('DOMContentLoaded', () => {
    // Sidebar search box currently has no backend endpoint to hit; redirect
    // to the blog index with a query string so the controller can pick it
    // up once search is implemented.
    document.querySelectorAll('[data-blog-search]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const value = form.querySelector('input')?.value?.trim();
            if (!value) return;
            const url = new URL(window.location.href);
            url.searchParams.set('q', value);
            window.location.href = url.toString();
        });
    });

    // Mini newsletter form is decorative until a subscription endpoint exists.
    document.querySelectorAll('[data-newsletter-mini]').forEach((form) => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            const input = form.querySelector('input');
            if (input) {
                input.value = '';
                input.placeholder = window.translations?.thanks_subscribing || 'Thanks for subscribing!';
            }
        });
    });
});
