import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/inventory.css',
                'resources/css/schedule.css',
                'resources/css/schedule-cards.css',
                'resources/css/login.css',
                'resources/css/rekap.css',
                'resources/css/assignment.css',
                'resources/js/app.js',
                'resources/js/inventory.js',
                'resources/js/schedule.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
    },
});