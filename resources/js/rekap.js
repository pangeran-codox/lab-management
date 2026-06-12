/**
 * rekap.js
 * Chart.js untuk halaman rekap penggunaan lab
 */

import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

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
