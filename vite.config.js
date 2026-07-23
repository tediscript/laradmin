import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        // Bind on all interfaces so Vite's dev server / HMR is reachable from
        // the host browser when running inside the Sail container.
        host: '0.0.0.0',
    },
});
