@extends('finance.layouts.app')
@section('title', 'Laporan Bulanan — ' . $monthName)
@section('page-title', 'Laporan Bulanan')

@push('styles')
<style>
/* ── Filter Bar ── */
.filter-bar {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 20px; flex-wrap: wrap;
}
.filter-bar select, .filter-bar input {
    padding: 8px 12px; border: 1px solid var(--border);
    border-radius: 9px; font-family: 'Sora', sans-serif;
    font-size: 13px; background: var(--surface); color: var(--text);
    outline: none; transition: border 0.15s;
}
.filter-bar select:focus, .filter-bar input:focus { border-color: var(--accent); }

/* ── Summary Cards ── */
.summary-grid {
    display: grid; grid-template-columns: repeat(4, 1fr);
    gap: 14px; margin-bottom: 20px;
}
.sum-card {
    background: var(--surface); border-radius: var(--radius);
    border: 1px solid var(--border); padding: 18px 20px;
    box-shadow: var(--shadow-sm);
}
.sum-card-label {
    font-size: 11px; font-weight: 600; color: var(--muted);
    text-transform: uppercase; letter-spacing: .06em; margin-bottom: 8px;
}
.sum-card-value {
    font-family: 'JetBrains Mono', monospace;
    font-size: 22px; font-weight: 700; line-height: 1;
    margin-bottom: 6px;
}
.sum-card-value.income  { color: var(--accent2); }
.sum-card-value.expense { color: var(--red); }
.sum-card-value.net-pos { color: var(--accent2); }
.sum-card-value.net-neg { color: var(--red); }
.sum-card-value.neutral { color: var(--text); }
.sum-card-change {
    font-size: 11px; display: flex; align-items: center; gap: 4px;
}
.sum-card-change.up   { color: var(--accent2); }
.sum-card-change.down { color: var(--red); }
.sum-card-change.neu  { color: var(--muted); }

/* ── Two Column Layout ── */
.two-col {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 16px; margin-bottom: 16px;
}

/* ── Category List ── */
.cat-list { display: flex; flex-direction: column; gap: 8px; }
.cat-item {
    display: flex; align-items: center; gap: 10px;
}
.cat-dot {
    width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
}
.cat-name { font-size: 13px; flex: 1; color: var(--text); }
.cat-count { font-size: 11px; color: var(--muted); margin-right: 8px; }
.cat-amount {
    font-family: 'JetBrains Mono', monospace;
    font-size: 13px; font-weight: 600;
}
.cat-bar-wrap {
    height: 4px; background: var(--border); border-radius: 999px;
    overflow: hidden; margin-top: 3px;
}
.cat-bar { height: 100%; border-radius: 999px; transition: width 0.6s ease; }

/* ── Chart ── */
.chart-wrap { position: relative; height: 200px; }

/* ── Transactions Table ── */
.trx-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.trx-table thead th {
    padding: 10px 12px; text-align: left;
    font-size: 10px; font-weight: 700; color: var(--muted);
    text-transform: uppercase; letter-spacing: .06em;
    background: var(--bg); border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
.trx-table tbody tr { border-bottom: 1px solid var(--border); transition: background 0.1s; }
.trx-table tbody tr:hover { background: var(--bg); }
.trx-table tbody td { padding: 10px 12px; vertical-align: middle; }
.trx-table tbody tr:last-child { border-bottom: none; }

.badge {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 3px 9px; border-radius: 999px;
    font-size: 10px; font-weight: 700;
}
.badge-income  { background: rgba(0,196,104,0.12); color: var(--accent2); }
.badge-expense { background: var(--red-dim); color: var(--red); }

.trx-code { font-family: 'JetBrains Mono', monospace; font-size: 11px; color: var(--muted); }
.trx-amount { font-family: 'JetBrains Mono', monospace; font-weight: 700; }
.trx-amount.income  { color: var(--accent2); }
.trx-amount.expense { color: var(--red); }

/* ── Budget Table ── */
.budget-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.budget-table th {
    padding: 10px 12px; text-align: left;
    font-size: 10px; font-weight: 700; color: var(--muted);
    text-transform: uppercase; letter-spacing: .06em;
    background: var(--bg); border-bottom: 1px solid var(--border);
}
.budget-table td { padding: 10px 12px; border-bottom: 1px solid var(--border); }
.budget-table tr:last-child td { border-bottom: none; }
.progress { height: 5px; background: var(--border); border-radius: 999px; overflow: hidden; }
.progress-bar {
    height: 100%; border-radius: 999px;
    background: var(--accent2); transition: width 0.5s ease;
}
.progress-bar.warn   { background: var(--amber); }
.progress-bar.danger { background: var(--red); }

/* ── Comparison ── */
.compare-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 8px;
}
.compare-item {
    background: var(--bg); border-radius: 10px;
    padding: 12px 14px; border: 1px solid var(--border);
}
.compare-label { font-size: 11px; color: var(--muted); margin-bottom: 4px; }
.compare-val {
    font-family: 'JetBrains Mono', monospace;
    font-size: 15px; font-weight: 700;
}

/* ── Empty ── */
.empty { text-align: center; padding: 36px; color: var(--muted); font-size: 13px; }

/* ── Responsive ── */
@media (max-width: 900px) {
    .summary-grid { grid-template-columns: 1fr 1fr; }
    .two-col      { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .summary-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')

{{-- ── Filter Bar ── --}}
<form method="GET" action="{{ route('finance.laporan.index') }}" class="filter-bar">
    <select name="month" onchange="this.form.submit()">
        @foreach($availableMonths as $m)
            <option value="{{ $m['month'] }}"
                    data-year="{{ $m['year'] }}"
                    {{ $m['selected'] ? 'selected' : '' }}>
                {{ $m['label'] }}
            </option>
        @endforeach
    </select>
    <input type="hidden" name="year" id="yearInput" value="{{ $year }}">
    <a href="{{ route('finance.laporan.index', ['month' => $prevMonth, 'year' => $prevYear]) }}"
       class="btn" style="background:var(--bg);border:1px solid var(--border);color:var(--text)">
        ← Bulan Sebelumnya
    </a>
    @if($month !== now()->month || $year !== now()->year)
    <a href="{{ route('finance.laporan.index') }}" class="btn btn-primary">
        Bulan Ini
    </a>
    @endif
</form>

<script>
document.querySelector('select[name=month]').addEventListener('change', function() {
    const opt = this.options[this.selectedIndex];
    document.getElementById('yearInput').value = opt.dataset.year;
    this.form.submit();
});
</script>

{{-- ── Summary Cards ── --}}
<div class="summary-grid">
    {{-- Income --}}
    <div class="sum-card">
        <div class="sum-card-label">Total Pemasukan</div>
        <div class="sum-card-value income">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
        @if($incomeChange !== null)
        <div class="sum-card-change {{ $incomeChange >= 0 ? 'up' : 'down' }}">
            {{ $incomeChange >= 0 ? '↑' : '↓' }} {{ abs($incomeChange) }}% vs bulan lalu
        </div>
        @else
        <div class="sum-card-change neu">— Tidak ada data bulan lalu</div>
        @endif
    </div>

    {{-- Expense --}}
    <div class="sum-card">
        <div class="sum-card-label">Total Pengeluaran</div>
        <div class="sum-card-value expense">Rp {{ number_format($totalExpense, 0, ',', '.') }}</div>
        @if($expenseChange !== null)
        <div class="sum-card-change {{ $expenseChange <= 0 ? 'up' : 'down' }}">
            {{ $expenseChange >= 0 ? '↑' : '↓' }} {{ abs($expenseChange) }}% vs bulan lalu
        </div>
        @else
        <div class="sum-card-change neu">— Tidak ada data bulan lalu</div>
        @endif
    </div>

    {{-- Net --}}
    <div class="sum-card">
        <div class="sum-card-label">Selisih (Net)</div>
        <div class="sum-card-value {{ $netBalance >= 0 ? 'net-pos' : 'net-neg' }}">
            {{ $netBalance >= 0 ? '+' : '' }}Rp {{ number_format($netBalance, 0, ',', '.') }}
        </div>
        <div class="sum-card-change neu">
            Bulan lalu: {{ $prevNet >= 0 ? '+' : '' }}Rp {{ number_format($prevNet, 0, ',', '.') }}
        </div>
    </div>

    {{-- Transactions Count --}}
    <div class="sum-card">
        <div class="sum-card-label">Jumlah Transaksi</div>
        <div class="sum-card-value neutral">{{ $transactions->count() }}</div>
        <div class="sum-card-change neu">
            {{ $transactions->where('type', 'income')->count() }} masuk ·
            {{ $transactions->where('type', 'expense')->count() }} keluar
        </div>
    </div>
</div>

{{-- ── Charts & Category Breakdown ── --}}
<div class="two-col" style="margin-bottom:16px">

    {{-- Trend Harian --}}
    <div class="card">
        <div class="card-head">
            <span class="card-title">📈 Trend Harian — {{ $monthName }}</span>
        </div>
        <div class="card-body">
            <div class="chart-wrap">
                <canvas id="dailyChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Perbandingan Bulan Lalu --}}
    <div class="card">
        <div class="card-head">
            <span class="card-title">📊 Perbandingan Bulan Lalu</span>
        </div>
        <div class="card-body">
            <div class="compare-grid">
                <div class="compare-item">
                    <div class="compare-label">Pemasukan {{ $monthName }}</div>
                    <div class="compare-val" style="color:var(--accent2)">
                        Rp {{ number_format($totalIncome, 0, ',', '.') }}
                    </div>
                </div>
                <div class="compare-item">
                    <div class="compare-label">Pemasukan Bulan Lalu</div>
                    <div class="compare-val" style="color:var(--muted)">
                        Rp {{ number_format($prevIncome, 0, ',', '.') }}
                    </div>
                </div>
                <div class="compare-item">
                    <div class="compare-label">Pengeluaran {{ $monthName }}</div>
                    <div class="compare-val" style="color:var(--red)">
                        Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                </div>
                <div class="compare-item">
                    <div class="compare-label">Pengeluaran Bulan Lalu</div>
                    <div class="compare-val" style="color:var(--muted)">
                        Rp {{ number_format($prevExpense, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            @if($period)
            <div style="margin-top:16px;padding-top:14px;border-top:1px solid var(--border)">
                <div class="compare-label" style="margin-bottom:8px">Realisasi vs Anggaran</div>
                @php
                    $budgetPct = $period->total_budget > 0
                        ? round(($totalExpense / $period->total_budget) * 100, 1) : 0;
                @endphp
                <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:4px">
                    <span>Terpakai {{ $budgetPct }}%</span>
                    <span style="color:var(--muted)">Anggaran: Rp {{ number_format($period->total_budget, 0, ',', '.') }}</span>
                </div>
                <div class="progress">
                    <div class="progress-bar {{ $budgetPct > 90 ? 'danger' : ($budgetPct > 75 ? 'warn' : '') }}"
                         style="width:{{ min(100, $budgetPct) }}%"></div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Category Breakdown ── --}}
<div class="two-col" style="margin-bottom:16px">

    {{-- Income by Category --}}
    <div class="card">
        <div class="card-head">
            <span class="card-title">💰 Pemasukan per Kategori</span>
        </div>
        <div class="card-body">
            @if($incomeByCategory->isEmpty())
                <div class="empty">Tidak ada data pemasukan</div>
            @else
            <div class="cat-list">
                @foreach($incomeByCategory as $cat)
                @php $pct = $totalIncome > 0 ? round(($cat['total'] / $totalIncome) * 100) : 0; @endphp
                <div>
                    <div class="cat-item">
                        <div class="cat-dot" style="background:{{ $cat['color'] }}"></div>
                        <span class="cat-name">{{ $cat['name'] }}</span>
                        <span class="cat-count">{{ $cat['count'] }}x</span>
                        <span class="cat-amount" style="color:var(--accent2)">
                            Rp {{ number_format($cat['total'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="cat-bar-wrap">
                        <div class="cat-bar" style="width:{{ $pct }}%;background:{{ $cat['color'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- Expense by Category --}}
    <div class="card">
        <div class="card-head">
            <span class="card-title">💸 Pengeluaran per Kategori</span>
        </div>
        <div class="card-body">
            @if($expenseByCategory->isEmpty())
                <div class="empty">Tidak ada data pengeluaran</div>
            @else
            <div class="cat-list">
                @foreach($expenseByCategory as $cat)
                @php $pct = $totalExpense > 0 ? round(($cat['total'] / $totalExpense) * 100) : 0; @endphp
                <div>
                    <div class="cat-item">
                        <div class="cat-dot" style="background:{{ $cat['color'] }}"></div>
                        <span class="cat-name">{{ $cat['name'] }}</span>
                        <span class="cat-count">{{ $cat['count'] }}x</span>
                        <span class="cat-amount" style="color:var(--red)">
                            Rp {{ number_format($cat['total'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="cat-bar-wrap">
                        <div class="cat-bar" style="width:{{ $pct }}%;background:{{ $cat['color'] }}"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ── Anggaran per Kategori ── --}}
@if($budgets->isNotEmpty())
<div class="card" style="margin-bottom:16px">
    <div class="card-head">
        <span class="card-title">🎯 Realisasi Anggaran per Kategori — {{ $monthName }}</span>
    </div>
    <div class="card-body" style="padding:0">
        <table class="budget-table">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th class="tc">Anggaran</th>
                    <th class="tc">Terpakai</th>
                    <th class="tc">Sisa</th>
                    <th style="width:180px">Progress</th>
                </tr>
            </thead>
            <tbody>
                @foreach($budgets as $budget)
                @php
                    $used    = (float) $budget->used_amount;
                    $amount  = (float) $budget->amount;
                    $remain  = $amount - $used;
                    $pct     = $amount > 0 ? min(100, round(($used / $amount) * 100)) : 0;
                    $barClass = $pct >= 90 ? 'danger' : ($pct >= 75 ? 'warn' : '');
                @endphp
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:8px;height:8px;border-radius:50%;background:{{ $budget->category?->color ?? '#6b7280' }}"></div>
                            {{ $budget->category?->name ?? '-' }}
                        </div>
                    </td>
                    <td class="tc" style="font-family:'JetBrains Mono',monospace;font-size:12px">
                        Rp {{ number_format($amount, 0, ',', '.') }}
                    </td>
                    <td class="tc" style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--red)">
                        Rp {{ number_format($used, 0, ',', '.') }}
                    </td>
                    <td class="tc" style="font-family:'JetBrains Mono',monospace;font-size:12px;color:{{ $remain >= 0 ? 'var(--accent2)' : 'var(--red)' }}">
                        {{ $remain >= 0 ? '' : '-' }}Rp {{ number_format(abs($remain), 0, ',', '.') }}
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div class="progress" style="flex:1">
                                <div class="progress-bar {{ $barClass }}" style="width:{{ $pct }}%"></div>
                            </div>
                            <span style="font-size:11px;color:var(--muted);width:35px;text-align:right">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ── Daftar Transaksi Lengkap ── --}}
<div class="card">
    <div class="card-head">
        <span class="card-title">📋 Semua Transaksi — {{ $monthName }}</span>
        <span style="font-size:12px;color:var(--muted)">{{ $transactions->count() }} transaksi</span>
    </div>
    <div class="card-body" style="padding:0;overflow-x:auto">
        @if($transactions->isEmpty())
            <div class="empty">Tidak ada transaksi di bulan ini</div>
        @else
        <table class="trx-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kode</th>
                    <th>Tipe</th>
                    <th>Kategori</th>
                    <th>Deskripsi</th>
                    <th>Akun</th>
                    <th class="tc">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $trx)
                <tr>
                    <td style="white-space:nowrap;color:var(--muted);font-size:12px">
                        {{ $trx->transaction_date->format('d M Y') }}
                    </td>
                    <td>
                        <span class="trx-code">{{ $trx->code }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $trx->type }}">
                            {{ $trx->type === 'income' ? '↑ Masuk' : '↓ Keluar' }}
                        </span>
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:6px">
                            @if($trx->category?->color)
                            <div style="width:7px;height:7px;border-radius:50%;background:{{ $trx->category->color }};flex-shrink:0"></div>
                            @endif
                            <span style="font-size:12px">{{ $trx->category?->name ?? '—' }}</span>
                        </div>
                    </td>
                    <td style="max-width:200px">
                        <div style="font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis"
                             title="{{ $trx->description }}">
                            {{ $trx->description }}
                        </div>
                        @if($trx->notes)
                        <div style="font-size:11px;color:var(--muted)">{{ $trx->notes }}</div>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--muted)">
                        {{ $trx->account?->name ?? '—' }}
                    </td>
                    <td class="tc">
                        <span class="trx-amount {{ $trx->type }}">
                            {{ $trx->type === 'income' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Trend Harian Chart
const ctx = document.getElementById('dailyChart');
if (ctx) {
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($dailyLabels),
            datasets: [
                {
                    label: 'Pemasukan',
                    data: @json($dailyIncome),
                    borderColor: '#00c468',
                    backgroundColor: 'rgba(0,196,104,0.08)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#00c468',
                },
                {
                    label: 'Pengeluaran',
                    data: @json($dailyExpense),
                    borderColor: '#ff4d6d',
                    backgroundColor: 'rgba(255,77,109,0.08)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: '#ff4d6d',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', labels: { font: { size: 11 }, boxWidth: 12 } },
                tooltip: {
                    callbacks: {
                        label: ctx => ctx.dataset.label + ': Rp ' + ctx.raw.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: v => 'Rp ' + (v >= 1000000 ? (v/1000000).toFixed(1)+'jt' : v.toLocaleString('id-ID')),
                        font: { size: 10 }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            }
        }
    });
}
</script>
@endpush
