/**
 * rekap.js
 * Chart.js untuk halaman rekap penggunaan lab
 */

import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

const donutColors = [
    '#00693E', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6',
    '#06b6d4', '#10b981', '#ec4899', '#14b8a6', '#f97316',
];

window.donutCharts = [];

/* ── Line chart pemakaian harian ── */
let usageChart;

function initUsageChart() {
    const ctx = document.getElementById('usageChart');
    if (!ctx) return;

    const d = window.rekapChartData;
    if (!d) return;

    const colors = [
        '#22c55e','#3b82f6','#a855f7','#f59e0b',
        '#ef4444','#06b6d4','#84cc16','#ec4899',
    ];

    const datasets = d.labData.map((lab, idx) => ({
        label: lab.resource.name,
        data:  lab.dailyData.map(day => day.total),
        backgroundColor: colors[idx % colors.length] + '20',
        borderColor:     colors[idx % colors.length],
        borderWidth: 2,
        tension: 0.3,
        fill: true,
    }));

    if (usageChart) usageChart.destroy();

    // Buat label tanggal sesuai bulan
    const daysInMonth = new Date(d.currentYear, d.currentMonth, 0).getDate();
    const labels = Array.from({ length: daysInMonth }, (_, i) => i + 1);

    usageChart = new Chart(ctx, {
        type: 'line',
        data: { labels, datasets },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: { mode: 'index', intersect: false },
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Slot Terpakai' } },
                x: { title: { display: true, text: 'Tanggal' } },
            },
            interaction: { mode: 'nearest', axis: 'x', intersect: false },
        },
    });
}

/* ── Donut chart proporsi pengajar per panel ── */
function initDonutCharts() {
    const panels = document.querySelectorAll('.panel');

    panels.forEach((panel, idx) => {
        // donut-content sekarang di dalam insight-card di dalam panel
        const donutContent = panel.querySelector('.insight-card .donut-content');
        if (!donutContent) return;

        const canvas          = donutContent.querySelector('.donut-canvas');
        const legendContainer = donutContent.querySelector('.donut-legend');
        if (!canvas || !legendContainer) return;

        const chartKey = 'panel_' + idx;
        if (window.donutCharts[chartKey]) {
            window.donutCharts[chartKey].destroy();
            window.donutCharts[chartKey] = null;
        }

        const teacherUsage = JSON.parse(canvas.dataset.teacherUsage || '{}');
        const totalUsed    = parseInt(canvas.dataset.totalUsed || 0);
        const labels = Object.keys(teacherUsage);
        const data   = Object.values(teacherUsage);
        const colors = labels.map((_, i) => donutColors[i % donutColors.length]);

        if (labels.length === 0) return;

        // Build legend
        legendContainer.innerHTML = '';
        labels.forEach((name, i) => {
            const count = data[i];
            const pct   = totalUsed > 0 ? ((count / totalUsed) * 100).toFixed(1) : 0;
            const item  = document.createElement('div');
            item.className = 'donut-legend-item';
            item.innerHTML = `
                <div class="donut-legend-color" style="background:${colors[i]}"></div>
                <div class="donut-legend-name">${name}</div>
                <div class="donut-legend-count">${count} (${pct}%)</div>
            `;
            legendContainer.appendChild(item);
        });

        window.donutCharts[chartKey] = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{ data, backgroundColor: colors, borderWidth: 2, borderColor: '#ffffff' }],
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(13,36,22,0.95)',
                        titleColor: '#ffffff',
                        bodyColor:  '#cce4f0',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => {
                                const pct = totalUsed > 0
                                    ? ((ctx.raw / totalUsed) * 100).toFixed(1) : 0;
                                return `${ctx.label}: ${ctx.raw} sesi (${pct}%)`;
                            },
                        },
                    },
                },
            },
        });
    });
}

/* ── Init semua ── */
function initAll() {
    initUsageChart();
    initDonutCharts();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAll);
} else {
    initAll();
}

// Expose agar bisa dipanggil ulang saat tab switch
window.initDonutCharts = initDonutCharts;
