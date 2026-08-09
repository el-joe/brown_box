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
                'resources/css/admin/app.css',
                'resources/js/admin/app.js',
                'resources/js/admin/form-handler.js',
                'resources/css/website/app.css',
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
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
