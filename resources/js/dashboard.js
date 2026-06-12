/**
 * dashboard.js
 * Chart.js charts & sparklines — Lab Management Dashboard
 * Chart.js diimport via npm (bukan CDN)
 */

import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

document.addEventListener('DOMContentLoaded', function () {
    const d  = window.dashboardData;
    if (!d) return;

    const G  = '#639922';
    const GM = '#C0DD97';
    const AM = '#BA7517';

    // ── SPARKLINES ────────────────────────────────────────────────────────────
    function sparkline(id, data, color) {
        const el = document.getElementById(id);
        if (!el) return;
        new Chart(el, {
            type: 'line',
            data: {
                labels: data.map((_, i) => i),
                datasets: [{
                    data,
                    borderColor: color,
                    borderWidth: 1.5,
                    tension: 0.45,
                    pointRadius: 0,
                    fill: true,
                    backgroundColor: color + '18'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { enabled: false } },
                scales: { x: { display: false }, y: { display: false } },
                animation: false
            }
        });
    }

    sparkline('sp1', [5, 5, 6, 6, 6, 7, d.totalLab],            G);
    sparkline('sp2', [28, 30, 33, 35, 38, 40, d.totalSchedule],  G);
    sparkline('sp3', [3, 5, 2, 4, 6, 3, d.pendingBook],          AM);
    sparkline('sp4', [2, 4, 3, 5, 3, 4, d.todayBook],            G);

    // ── STACKED BAR — penggunaan lab per hari bulan ini ───────────────────────
    const barEl = document.getElementById('barChart');
    if (barEl && d.monthLabels && d.dailyBookings) {
        new Chart(barEl, {
            type: 'bar',
            data: {
                labels: d.monthLabels,
                datasets: [
                    {
                        label: 'Booking',
                        data: d.dailyBookings,
                        backgroundColor: '#EAF3DE',
                        borderColor: G,
                        borderWidth: 1,
                        borderRadius: 3,
                        stack: 'a'
                    },
                    {
                        label: 'Jadwal',
                        data: d.dailySchedules,
                        backgroundColor: GM,
                        borderColor: '#3B6D11',
                        borderWidth: 1,
                        borderRadius: 3,
                        stack: 'a'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 10,
                            boxHeight: 10,
                            font: { size: 10 },
                            color: '#9ca3af',
                            padding: 12
                        }
                    },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 }, color: '#9ca3af', maxTicksLimit: 10 },
                        stacked: true
                    },
                    y: {
                        grid: { color: 'rgba(0,0,0,0.04)' },
                        ticks: { font: { size: 10 }, color: '#9ca3af', stepSize: 1 },
                        beginAtZero: true,
                        stacked: true
                    }
                }
            }
        });
    }
});


// ── REALTIME BOOKING NOTIFICATION via Reverb ──────────────────────────────────
(function () {
    if (!window.Echo) return;

    // ── Toast helper ─────────────────────────────────────────────────────────
    function createToast(data) {
        // Container
        let container = document.getElementById('db-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'db-toast-container';
            container.style.cssText = [
                'position:fixed',
                'top:20px',
                'right:20px',
                'z-index:9999',
                'display:flex',
                'flex-direction:column',
                'gap:10px',
                'pointer-events:none',
            ].join(';');
            document.body.appendChild(container);
        }

        // Toast element
        const toast = document.createElement('div');
        toast.style.cssText = [
            'background:#fff',
            'border:0.5px solid rgba(0,0,0,0.10)',
            'border-left:3px solid #BA7517',
            'border-radius:10px',
            'padding:12px 16px',
            'min-width:280px',
            'max-width:340px',
            'box-shadow:0 4px 20px rgba(0,0,0,0.10)',
            'pointer-events:all',
            'cursor:pointer',
            'transform:translateX(120%)',
            'transition:transform 0.3s cubic-bezier(0.34,1.56,0.64,1)',
            'display:flex',
            'gap:10px',
            'align-items:flex-start',
        ].join(';');

        toast.innerHTML = `
            <div style="width:32px;height:32px;border-radius:8px;background:#FAEEDA;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#BA7517" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0">
                <p style="font-size:12px;font-weight:500;color:#1a1a1a;margin:0 0 2px">Booking Baru Masuk</p>
                <p style="font-size:11px;color:#444;margin:0 0 1px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                    <strong>${data.teacher_name}</strong> — ${data.resource}
                </p>
                <p style="font-size:11px;color:#666;margin:0">${data.title}</p>
                <p style="font-size:10px;color:#BA7517;margin:3px 0 0;font-weight:500">Menunggu persetujuan</p>
            </div>
            <button onclick="this.closest('[data-toast]').remove()" style="background:none;border:none;cursor:pointer;color:#aaa;font-size:16px;line-height:1;padding:0;flex-shrink:0">×</button>
        `;
        toast.setAttribute('data-toast', '1');

        // Klik toast → buka halaman booking pending
        toast.addEventListener('click', function (e) {
            if (e.target.tagName !== 'BUTTON') {
                window.location.href = '/booking?status=pending';
            }
        });

        container.appendChild(toast);

        // Animasi masuk
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.style.transform = 'translateX(0)';
            });
        });

        // Update badge pending di header jika ada
        const badge = document.querySelector('.badge-count');
        if (badge) {
            badge.textContent = parseInt(badge.textContent || '0') + 1;
        }

        // Auto dismiss setelah 6 detik
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            setTimeout(() => toast.remove(), 350);
        }, 6000);
    }

    // ── Listen channel bookings ───────────────────────────────────────────────
    window.Echo.channel('bookings')
        .listen('.booking.created', function (data) {
            createToast(data);
        });
})();