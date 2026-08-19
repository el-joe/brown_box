import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/css/admin/app.ltr.css',
                'resources/css/admin/app.rtl.css',
                'resources/js/admin/app.js',
                'resources/js/admin/form-handler.js',
                'resources/css/website/app.ltr.css',
                'resources/css/website/app.rtl.css',
                'resources/js/website/app.js',
                'resources/js/website/home.js',
                'resources/js/website/products.js',
                'resources/js/website/product-show.js',
                'resources/js/website/cart.js',
                'resources/js/website/checkout.js',
                'resources/js/website/auth.js',
                'resources/js/website/account.js',
                'resources/js/website/blog.js',
                'resources/js/website/contact.js',
                'resources/js/website/faq.js',
                'resources/js/website/track-order.js',
            ],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        tailwindcss(),
    ],
    build: {
        chunkSizeWarningLimit: 600,
        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('node_modules/swiper')) return 'vendor-swiper';
                    if (id.includes('node_modules/alpinejs') || id.includes('node_modules/@alpinejs')) return 'vendor-alpine';
                    if (id.includes('node_modules/toastr')) return 'vendor-toastr';
                },
            },
        },
    },
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
