import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // ── Global ──────────────────────────────────
                'resources/css/app.css',
                'resources/js/app.js',

                // ── Halaman Jadwal Publik ────────────────────
                'resources/css/schedule.css',
                'resources/css/schedule-cards.css',
                'resources/js/schedule.js',

                // ── Login ────────────────────────────────────
                'resources/css/login.css',

                // ── Dashboard (admin) ────────────────────────
                'resources/css/dashboard.css',
                'resources/js/dashboard.js',

                // ── Jadwal Admin ─────────────────────────────
                'resources/css/jadwal.css',
                'resources/js/jadwal.js',

                // ── Booking ──────────────────────────────────
                'resources/css/booking.css',
                'resources/js/booking.js',

                // ── Inventaris ───────────────────────────────
                'resources/css/inventory.css',
                'resources/js/inventory.js',

                // ── Inventaris Admin ─────────────────────────
                'resources/css/inventoryadmin.css',
                'resources/js/inventoryadmin.js',

                // ── Rekap ────────────────────────────────────
                'resources/css/rekap.css',
                'resources/js/rekap.js',

                // ── Assignment/Tugas ─────────────────────────
                'resources/css/assignment.css',

                // ── Kelas ────────────────────────────────────
                'resources/css/kelas.css',
                'resources/js/kelas.js',

                // ── Sekolah/Organisasi ───────────────────────
                'resources/css/sekolah.css',
                'resources/js/sekolah.js',

                // ── Guru ─────────────────────────────────────
                'resources/css/teacher.css',
                'resources/js/teacher.js',

                // ── Jadwal Penting ───────────────────────────
                'resources/css/important.css',
                'resources/js/important.js',

                // ── Users ────────────────────────────────────
                'resources/css/users.css',
                'resources/js/users.js',
            ],
            refresh: true,
        }),
    ],

    build: {
        // Target browser modern — output lebih kecil (tidak perlu polyfill ES5)
        target: ['es2020', 'edge88', 'firefox78', 'chrome87', 'safari14'],

        // Pisahkan vendor chunk agar browser bisa cache Alpine.js terpisah
        rollupOptions: {
            output: {
                // Naming konsisten: assets/[name]-[hash].js
                chunkFileNames:  'assets/[name]-[hash].js',
                entryFileNames:  'assets/[name]-[hash].js',
                assetFileNames:  'assets/[name]-[hash][extname]',
            },
        },

        // Minify pakai esbuild (default Vite, lebih cepat dari terser)
        minify: 'esbuild',

        // CSS code splitting — setiap entrypoint CSS jadi file terpisah
        cssCodeSplit: true,

        // Sourcemap off di production (jangan expose source code)
        sourcemap: false,

        // Batas peringatan ukuran chunk: 500 KB
        chunkSizeWarningLimit: 500,
    },

    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: process.env.VITE_HMR_HOST || 'localhost',
            port: 5173,
            protocol: 'ws',
        },
        cors: {
            origin: '*',
        },
        // Proxy request font & asset statis ke Nginx agar tidak 404 di dev mode
        proxy: {
            '/fonts': {
                target: 'http://lab_nginx:80',
                changeOrigin: true,
            },
            '/images': {
                target: 'http://lab_nginx:80',
                changeOrigin: true,
            },
            '/storage': {
                target: 'http://lab_nginx:80',
                changeOrigin: true,
            },
        },
    },
});
