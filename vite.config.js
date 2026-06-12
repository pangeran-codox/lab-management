import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/inventory.css',
                'resources/css/inventoryadmin.css',   // ← tambah
                'resources/css/schedule.css',
                'resources/css/schedule-cards.css',
                'resources/css/login.css',
                'resources/css/rekap.css',
                'resources/css/assignment.css',
                'resources/css/dashboard.css',
                'resources/css/jadwal.css',
                'resources/css/booking.css',
                'resources/css/kelas.css',
                'resources/css/sekolah.css',
                'resources/css/teacher.css',
                'resources/css/important.css',
                'resources/js/app.js',
                'resources/js/inventory.js',
                'resources/js/inventoryadmin.js',     // ← tambah
                'resources/js/schedule.js',
                'resources/js/dashboard.js',
                'resources/js/jadwal.js',
                'resources/js/booking.js',
                'resources/js/kelas.js',
                'resources/js/sekolah.js',
                'resources/js/teacher.js',
                'resources/js/important.js',
                'resources/js/rekap.js',
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
});