/**
 * rekap.js
 * Chart.js untuk halaman rekap penggunaan lab
 */

import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

// Warna untuk donut chart
const donutColors = [
    '#00693E', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4', '#10b981',
    '#ec4899', '#14b8a6', '#f97316'
];

// Inisialisasi Chart.js (tanpa DOMContentLoaded karena script di-load di akhir)
const d = window.rekapChartData;
if (d) {
    let usageChart;

    function initUsageChart() {
        const ctx = document.getElementById('usageChart');
        if (!ctx) return;

        // Warna untuk setiap lab
        const colors = [
            '#22c55e', '#3b82f6', '#a855f7', '#f59e0b', '#ef4444', '#06b6d4', '#84cc16', '#ec4899'
        ];

        // Siapkan dataset
        const datasets = [];
        d.labData.forEach(function(lab, idx) {
            // Siapkan data per hari
            const data = lab.dailyData.map(function(day) {
                return day.total;
            });

            datasets.push({
                label: lab.resource.name,
                data: data,
                backgroundColor: colors[idx % colors.length] + '20',
                borderColor: colors[idx % colors.length],
                borderWidth: 2,
                tension: 0.3,
                fill: true
            });
        });

        // Hapus chart lama jika ada
        if (usageChart) {
            usageChart.destroy();
        }

        usageChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: getDaysInMonth(),
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Slot Terpakai'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }

    function getDaysInMonth() {
        const date = new Date(d.currentYear, d.currentMonth, 0);
        const days = [];
        for (let i = 1; i <= date.getDate(); i++) {
            days.push(i);
        }
        return days;
    }

    initUsageChart();
}

// Array untuk menyimpan semua donut chart instances
window.donutCharts = [];

// Fungsi untuk inisialisasi donut chart SEMUA PANEL saat halaman dimuat
function initDonutCharts() {
    console.log('🚀 Initializing ALL donut charts...');
    const panels = document.querySelectorAll('.panel');

    panels.forEach((panel, idx) => {
        console.log(`Processing panel #${idx}...`);
        const donutContent = panel.querySelector('.donut-content');
        if (!donutContent) {
            console.log(`⚠️ No donut-content found for panel #${idx}`);
            return;
        }

        const canvas = donutContent.querySelector('.donut-canvas');
        const legendContainer = donutContent.querySelector('.donut-legend');
        if (!canvas || !legendContainer) {
            console.log(`⚠️ Missing canvas or legend for panel #${idx}`);
            return;
        }

        // Destroy existing if any
        if (window.donutCharts[idx]) {
            window.donutCharts[idx].destroy();
        }

        // Get data
        const teacherUsage = JSON.parse(canvas.dataset.teacherUsage || '{}');
        const totalUsed = parseInt(canvas.dataset.totalUsed || 0);

        // Reset canvas dimensions
        canvas.width = 180;
        canvas.height = 180;
        canvas.style.width = '180px';
        canvas.style.height = '180px';

        // Prepare chart data
        const labels = Object.keys(teacherUsage);
        const data = Object.values(teacherUsage);
        const colors = labels.map((_, i) => donutColors[i % donutColors.length]);

        // Render legend
        legendContainer.innerHTML = '';
        labels.forEach((name, i) => {
            const count = data[i];
            const pct = totalUsed > 0 ? ((count / totalUsed) * 100).toFixed(1) : 0;
            const item = document.createElement('div');
            item.className = 'donut-legend-item';
            item.innerHTML = `
                <div class="donut-legend-color" style="background: ${colors[i]}"></div>
                <div class="donut-legend-name">${name}</div>
                <div class="donut-legend-count">${count} sesi (${pct}%)</div>
            `;
            legendContainer.appendChild(item);
        });

        // Create chart
        window.donutCharts[idx] = new Chart(canvas, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors,
                    borderWidth: 3,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: false,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(13, 36, 22, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#cce4f0',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: (ctx) => {
                                const pct = totalUsed > 0 ? ((ctx.raw / totalUsed) * 100).toFixed(1) : 0;
                                return `${ctx.label}: ${ctx.raw} sesi (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });

        console.log(`✅ Panel #${idx} donut chart initialized!`);
    });

    console.log('🎉 All donut charts initialized!');
}

// Panggil fungsi inisialisasi donut chart
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 Initializing donut charts...');
    initDonutCharts();
    console.log('✅ Donut charts initialized!');
});

// Also initialize after a small delay to ensure DOM is fully ready
setTimeout(function() {
    console.log('🔄 Re-initializing donut charts (delay)...');
    initDonutCharts();
    console.log('✅ Donut charts re-initialized!');
}, 500);
