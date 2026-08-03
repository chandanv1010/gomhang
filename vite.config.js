import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import sass from 'sass';

export default defineConfig({
    base: '',
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss', 
                'resources/js/app.js',
                'resources/css/app_backend.scss',
                'resources/js/app.backend.js'
            ],
            refresh: true,
        }),
    ],
    css: {
        preprocessorOptions: {
            scss: {
                sourceMap: true,   // 👈 bật sourcemap cho scss,
                silenceDeprecations: ['legacy-js-api']
            },
        },
        devSourcemap: true
    },
    build: {
        sourcemap: false, // Tắt hoàn toàn source map cho production build
    },
});
