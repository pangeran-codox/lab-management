@extends('finance.layouts.app')
@section('title', 'Transaksi — Keuangan Tim')
@section('page-title', 'Transaksi')

@push('styles')
<style>
@keyframes floatIn {
    0%   { opacity: 0; transform: translateY(20px) scale(0.98); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
@keyframes rowIn {
    from { opacity: 0; transform: translateX(-10px); }
    to   { opacity: 1; transform: translateX(0); }
}
@keyframes cardIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}
.float-in { opacity:0; animation: floatIn 0.5s cubic-bezier(0.34,1.56,0.64,1) forwards; }

/* ── Page Top ── */
.page-top {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px;
}

/* ── Summary Strip ── */
.summary-strip {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px; margin-bottom: 16px;
}
.sum-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 14px 18px;
    display: flex; align-items: center; gap: 12px;
    transition: transform 0.2s, box-shadow 0.2s;
    position: relative; overflow: hidden;
}
.sum-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-md); }
.sum-card::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
    border-radius: var(--radius) var(--radius) 0 0;
}
.sum-card.income::before  { background: linear-gradient(90deg, #00e87a, #00c468); }
.sum-card.expense::before { background: linear-gradient(90deg, #ff4d6d, #fb7185); }
.sum-card.balance::before { background: linear-gradient(90deg, var(--g1), var(--g2)); }

.sum-icon { width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.sum-icon svg { width: 17px; height: 17px; }
.sum-card.income  .sum-icon { background: #dcfce7; color: #16a34a; }
.sum-card.expense .sum-icon { background: #fff1f2; color: var(--red); }
.sum-card.balance .sum-icon { background: rgba(7,31,20,0.06); color: var(--g1); }
.sum-label { font-size: 10.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: var(--muted); margin-bottom: 3px; }
.sum-value { font-family: 'JetBrains Mono', monospace; font-size: 17px; font-weight: 600; }
.sum-card.income  .sum-value { color: #16a34a; }
.sum-card.expense .sum-value { color: var(--red); }
.sum-card.balance .sum-value { color: var(--text); }

/* ── Filter Bar ── */
.filter-bar {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 14px 18px;
    margin-bottom: 14px;
    display: flex; gap: 10px; flex-wrap: wrap; align-items: flex-end;
}
.filter-group { display: flex; flex-direction: column; gap: 4px; }
.filter-label { font-size: 10px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: 0.6px; }
.filter-ctrl {
    padding: 7px 11px; border: 1.5px solid var(--border); border-radius: 8px;
    font-family: 'Sora', sans-serif; font-size: 12px; color: var(--text);
    background: white; outline: none; transition: all 0.2s;
}
.filter-ctrl:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(0,232,122,0.1); }

/* ── Table Card (desktop) ── */
.table-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-sm);
}
.trx-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.trx-table thead tr { background: var(--bg); border-bottom: 2px solid var(--border); }
.trx-table thead th {
    padding: 11px 16px; text-align: left; font-size: 10px; font-weight: 700;
    color: var(--muted); text-transform: uppercase; letter-spacing: 0.8px; white-space: nowrap;
}
.trx-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s;
    animation: rowIn 0.3s ease both;
}
.trx-table tbody tr:last-child { border-bottom: none; }
.trx-table tbody tr:hover { background: #f8f6f2; }
.trx-table tbody td { padding: 12px 16px; vertical-align: middle; }

.code-chip {
    font-family: 'JetBrains Mono', monospace; font-size: 10.5px; color: var(--muted);
    background: var(--bg); border: 1px solid var(--border);
    padding: 3px 8px; border-radius: 6px; white-space: nowrap;
}
.type-pill {
    display: inline-flex; align-items: center; gap: 5px;
    padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
}
.type-pill.income  { background: #dcfce7; color: #16a34a; }
.type-pill.expense { background: #fff1f2; color: var(--red); }
.type-pill svg { width: 10px; height: 10px; }

.amt { font-family: 'JetBrains Mono', monospace; font-size: 13px; font-weight: 600; white-space: nowrap; }
.amt.income  { color: #16a34a; }
.amt.expense { color: var(--red); }

.del-btn {
    width: 30px; height: 30px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    background: none; border: 1px solid var(--border); color: var(--muted);
    cursor: pointer; transition: all 0.2s;
}
.del-btn:hover { background: #fff1f2; color: var(--red); border-color: rgba(255,77,109,0.3); transform: scale(1.1); }
.del-btn svg { width: 13px; height: 13px; }

.empty-row td { text-align: center; padding: 48px !important; color: var(--muted); font-size: 13px; }

/* ── Attachment thumb/link ── */
.attach-thumb {
    width: 34px; height: 34px; border-radius: 8px;
    object-fit: cover; border: 1.5px solid var(--border);
    cursor: zoom-in; transition: transform 0.15s, box-shadow 0.15s;
    display: block;
}
.attach-thumb:hover { transform: scale(1.08); box-shadow: var(--shadow-sm); }
.attach-none { font-size: 11px; color: var(--muted); }
.attach-chip {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600; color: var(--g1);
    text-decoration: none; padding: 4px 9px; border-radius: 20px;
    background: rgba(7,31,20,0.06); border: 1px solid var(--border);
    transition: all 0.15s;
}
.attach-chip:hover { background: var(--g1); color: white; border-color: var(--g1); }
.attach-chip svg { width: 11px; height: 11px; }

/* ── Lightbox ── */
.lightbox-overlay {
    display: none; position: fixed; inset: 0; z-index: 999;
    background: rgba(0,0,0,0.75);
    align-items: center; justify-content: center; padding: 24px;
    cursor: zoom-out;
}
.lightbox-overlay.open { display: flex; }
.lightbox-overlay img {
    max-width: 90vw; max-height: 90vh; border-radius: 10px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}

/* ── MOBILE CARD LIST ── */
.trx-card-list { display: none; }

.trx-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); overflow: hidden;
    box-shadow: var(--shadow-sm); margin-bottom: 10px;
    animation: cardIn 0.35s ease both;
    position: relative;
}
/* Colored left border */
.trx-card::before {
    content: ''; position: absolute;
    left: 0; top: 0; bottom: 0; width: 3px;
}
.trx-card.income::before  { background: linear-gradient(180deg, #00e87a, #00c468); }
.trx-card.expense::before { background: linear-gradient(180deg, #ff4d6d, #fb7185); }

.trx-card-top {
    display: flex; align-items: flex-start; justify-content: space-between;
    padding: 13px 14px 10px 16px; gap: 10px;
}
.trx-card-left { flex: 1; min-width: 0; }
.trx-card-desc { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.trx-card-meta { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.trx-card-cat  { font-size: 11px; color: var(--muted); }
.trx-card-date { font-size: 11px; color: var(--muted); }
.trx-card-sep  { width: 3px; height: 3px; border-radius: 50%; background: var(--border); }

.trx-card-right { text-align: right; flex-shrink: 0; }
.trx-card-amt {
    font-family: 'JetBrains Mono', monospace;
    font-size: 15px; font-weight: 700; line-height: 1;
    margin-bottom: 5px;
}
.trx-card-amt.income  { color: #16a34a; }
.trx-card-amt.expense { color: var(--red); }

.trx-card-foot {
    display: flex; align-items: center; justify-content: space-between;
    padding: 8px 14px 10px 16px;
    border-top: 1px solid var(--border);
    background: var(--bg);
}
.trx-card-code { font-family: 'JetBrains Mono', monospace; font-size: 10.5px; color: var(--muted); }
.trx-card-actions { display: flex; gap: 6px; align-items: center; }

.del-btn-sm {
    height: 28px; padding: 0 10px; border-radius: 7px;
    display: inline-flex; align-items: center; gap: 5px;
    background: none; border: 1px solid var(--border);
    color: var(--muted); cursor: pointer; font-size: 11px; font-weight: 600;
    font-family: 'Sora', sans-serif;
    transition: all 0.2s;
}
.del-btn-sm:hover { background: #fff1f2; color: var(--red); border-color: rgba(255,77,109,0.3); }
.del-btn-sm svg { width: 11px; height: 11px; }

/* ── Pagination ── */
.pag-wrap { padding: 14px 18px; border-top: 1px solid var(--border); display: flex; justify-content: center; }
.pag-wrap nav { display: flex; gap: 4px; flex-wrap: wrap; justify-content: center; }
.pag-wrap nav span, .pag-wrap nav a {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 34px; height: 34px; padding: 0 10px;
    border-radius: 8px; font-size: 12px; font-weight: 600;
    border: 1px solid var(--border); color: var(--text);
    text-decoration: none; transition: all 0.15s;
}
.pag-wrap nav a:hover { background: var(--bg); }
.pag-wrap nav span[aria-current] { background: var(--g1); color: white; border-color: var(--g1); }

/* ── Filter toggle mobile ── */
.filter-toggle-btn {
    display: none; width: 100%; padding: 11px 16px;
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius); margin-bottom: 10px;
    font-family: 'Sora', sans-serif; font-size: 13px; font-weight: 600;
    color: var(--text); cursor: pointer;
    justify-content: space-between; align-items: center;
    transition: all 0.2s;
}
.filter-toggle-btn:hover { border-color: var(--accent); }
.filter-toggle-btn svg { width: 16px; height: 16px; color: var(--muted); transition: transform 0.2s; }
.filter-toggle-btn.open svg { transform: rotate(180deg); }

.filter-collapsible { display: block; }

/* ── RESPONSIVE ── */
@media (max-width: 900px) {
    .summary-strip { grid-template-columns: 1fr 1fr; }
    .sum-card.balance { grid-column: span 2; }
}

@media (max-width: 600px) {
    /* Summary */
    .summary-strip { grid-template-columns: 1fr 1fr; gap: 8px; }
    .sum-card.balance { grid-column: span 2; }
    .sum-card { padding: 11px 12px; gap: 9px; }
    .sum-icon { width: 32px; height: 32px; border-radius: 8px; }
    .sum-value { font-size: 14px; }
    .sum-label { font-size: 9.5px; }

    /* Page top */
    .page-top { flex-direction: column; gap: 10px; align-items: stretch; }
    .page-top .btn { justify-content: center; }

    /* Filter */
    .filter-toggle-btn { display: flex; }
    .filter-collapsible { display: none; }
    .filter-collapsible.open { display: block; }
    .filter-bar { flex-direction: column; border-radius: 0 0 var(--radius) var(--radius); border-top: none; margin-top: -4px; padding-top: 12px; }
    .filter-group { width: 100%; }
    .filter-ctrl { width: 100%; }
    .filter-actions { display: flex; gap: 6px; }
    .filter-actions .btn { flex: 1; justify-content: center; }

    /* Sembunyikan tabel, tampilkan card */
    .table-card  { display: none; }
    .trx-card-list { display: block; }
}
</style>
@endpush

@section('content')

{{-- Page Top --}}
<div class="page-top float-in" style="animation-delay:.05s">
    <div style="font-size:13px;color:var(--muted);">
        Semua transaksi tim — <strong style="color:var(--text);">{{ now()->isoFormat('MMMM Y') }}</strong>
    </div>
    <a href="{{ route('finance.transactions.create') }}" class="btn btn-primary">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Tambah
    </a>
</div>

{{-- Summary --}}
<div class="summary-strip float-in" style="animation-delay:.1s">
    <div class="sum-card income">
        <div class="sum-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg></div>
        <div>
            <div class="sum-label">Pemasukan</div>
            <div class="sum-value">Rp {{ number_format($summary['total_income'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="sum-card expense">
        <div class="sum-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg></div>
        <div>
            <div class="sum-label">Pengeluaran</div>
            <div class="sum-value">Rp {{ number_format($summary['total_expense'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="sum-card balance">
        <div class="sum-icon"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
        <div>
            <div class="sum-label">Saldo Kas</div>
            <div class="sum-value">Rp {{ number_format($summary['kas_balance'], 0, ',', '.') }}</div>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="float-in" style="animation-delay:.15s">
    {{-- Mobile toggle button --}}
    <button class="filter-toggle-btn" id="filterToggle" onclick="toggleFilter()">
        <span style="display:flex;align-items:center;gap:8px;">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:15px;height:15px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            Filter Transaksi
            @if(request()->hasAny(['month','year','type','category_id']))
                <span style="background:var(--accent2);color:white;font-size:10px;padding:1px 7px;border-radius:20px;">Aktif</span>
            @endif
        </span>
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
    </button>

    <div class="filter-collapsible" id="filterCollapsible">
        <form method="GET" action="{{ route('finance.transactions.index') }}">
            <div class="filter-bar">
                <div class="filter-group">
                    <span class="filter-label">Bulan</span>
                    <select name="month" class="filter-ctrl">
                        @foreach(range(1,12) as $m)
                            <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group">
                    <span class="filter-label">Tahun</span>
                    <input type="number" name="year" value="{{ request('year', now()->year) }}" class="filter-ctrl" style="width:80px;" min="2024">
                </div>
                <div class="filter-group">
                    <span class="filter-label">Tipe</span>
                    <select name="type" class="filter-ctrl">
                        <option value="">Semua</option>
                        <option value="income"  {{ request('type') === 'income'  ? 'selected' : '' }}>Pemasukan</option>
                        <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                    </select>
                </div>
                <div class="filter-group">
                    <span class="filter-label">Kategori</span>
                    <select name="category_id" class="filter-ctrl">
                        <option value="">Semua</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-actions" style="display:flex;gap:6px;align-self:flex-end;">
                    <button type="submit" class="btn btn-primary" style="height:36px;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                        Filter
                    </button>
                    <a href="{{ route('finance.transactions.index') }}" class="btn btn-secondary" style="height:36px;">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════ DESKTOP TABLE ════════════════ --}}
<div class="table-card float-in" style="animation-delay:.2s">
    <div style="overflow-x:auto;">
        <table class="trx-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Kategori</th>
                    <th>Tipe</th>
                    <th>Jumlah</th>
                    <th>Dicatat</th>
                    <th>Lampiran</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $i => $trx)
                    <tr style="animation-delay:{{ $i * 40 }}ms">
                        <td><span class="code-chip">{{ $trx->code }}</span></td>
                        <td style="white-space:nowrap;color:var(--muted);font-size:12px;">
                            {{ $trx->transaction_date->format('d M Y') }}
                        </td>
                        <td style="font-weight:500;max-width:200px;">{{ $trx->description }}</td>
                        <td style="color:var(--muted);font-size:12px;">{{ $trx->category->name }}</td>
                        <td>
                            <span class="type-pill {{ $trx->type }}">
                                @if($trx->type === 'income')
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                                    Masuk
                                @else
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                                    Keluar
                                @endif
                            </span>
                        </td>
                        <td>
                            <span class="amt {{ $trx->type }}">
                                {{ $trx->type === 'income' ? '+' : '−' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td style="color:var(--muted);font-size:12px;">{{ $trx->created_by_name ?? '-' }}</td>
                        <td>
                            @if($trx->attachment)
                                <img src="{{ asset('storage/' . $trx->attachment) }}"
                                     alt="Lampiran {{ $trx->code }}"
                                     class="attach-thumb"
                                     onclick="openLightbox('{{ asset('storage/' . $trx->attachment) }}')"
                                     onerror="this.replaceWith(Object.assign(document.createElement('span'),{className:'attach-none',textContent:'Rusak'}))">
                            @else
                                <span class="attach-none">—</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('finance.transactions.destroy', $trx) }}"
                                  onsubmit="return confirm('Hapus transaksi {{ $trx->code }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="del-btn" title="Hapus">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr class="empty-row">
                        <td colspan="9">
                            <div style="font-size:32px;margin-bottom:10px;">🧾</div>
                            Belum ada transaksi pada periode ini.<br>
                            <a href="{{ route('finance.transactions.create') }}" style="color:var(--accent2);font-weight:700;text-decoration:none;">
                                Tambah transaksi pertama →
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
        <div class="pag-wrap">{{ $transactions->withQueryString()->links() }}</div>
    @endif
</div>

{{-- ════════════════ MOBILE CARD LIST ════════════════ --}}
<div class="trx-card-list float-in" style="animation-delay:.2s">
    @forelse($transactions as $i => $trx)
        <div class="trx-card {{ $trx->type }}" style="animation-delay:{{ $i * 50 }}ms">
            <div class="trx-card-top">
                <div class="trx-card-left">
                    <div class="trx-card-desc">{{ $trx->description }}</div>
                    <div class="trx-card-meta">
                        <span class="trx-card-cat">{{ $trx->category->name }}</span>
                        <span class="trx-card-sep"></span>
                        <span class="trx-card-date">{{ $trx->transaction_date->format('d M Y') }}</span>
                    </div>
                </div>
                <div class="trx-card-right">
                    <div class="trx-card-amt {{ $trx->type }}">
                        {{ $trx->type === 'income' ? '+' : '−' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}
                    </div>
                    <span class="type-pill {{ $trx->type }}" style="font-size:10px;padding:3px 8px;">
                        {{ $trx->type === 'income' ? '↑ Masuk' : '↓ Keluar' }}
                    </span>
                </div>
            </div>
            <div class="trx-card-foot">
                <span class="trx-card-code">{{ $trx->code }}</span>
                <div class="trx-card-actions">
                    @if($trx->attachment)
                        <a href="javascript:void(0)"
                           onclick="openLightbox('{{ asset('storage/' . $trx->attachment) }}')"
                           class="attach-chip">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            Foto
                        </a>
                    @endif
                    <span style="font-size:11px;color:var(--muted);">{{ $trx->created_by_name ?? '-' }}</span>
                    <form method="POST" action="{{ route('finance.transactions.destroy', $trx) }}"
                          onsubmit="return confirm('Hapus {{ $trx->code }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="del-btn-sm">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div style="text-align:center;padding:40px 20px;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);">
            <div style="font-size:32px;margin-bottom:10px;">🧾</div>
            <div style="font-size:13px;color:var(--muted);">Belum ada transaksi.<br>
                <a href="{{ route('finance.transactions.create') }}" style="color:var(--accent2);font-weight:700;text-decoration:none;">
                    Tambah sekarang →
                </a>
            </div>
        </div>
    @endforelse

    @if($transactions->hasPages())
        <div class="pag-wrap" style="background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);margin-top:4px;">
            {{ $transactions->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- ════════════════ LIGHTBOX (preview lampiran) ════════════════ --}}
<div class="lightbox-overlay" id="lightboxOverlay" onclick="closeLightbox()">
    <img id="lightboxImg" src="" alt="Preview lampiran">
</div>

@endsection

@push('scripts')
<script>
function toggleFilter() {
    const btn = document.getElementById('filterToggle');
    const col = document.getElementById('filterCollapsible');
    btn.classList.toggle('open');
    col.classList.toggle('open');
}

function openLightbox(src) {
    document.getElementById('lightboxImg').src = src;
    document.getElementById('lightboxOverlay').classList.add('open');
}

function closeLightbox() {
    document.getElementById('lightboxOverlay').classList.remove('open');
    document.getElementById('lightboxImg').src = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLightbox();
});
</script>
@endpush