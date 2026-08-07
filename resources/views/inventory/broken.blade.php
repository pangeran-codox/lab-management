<x-app-layout>
<x-slot name="title">Barang Rusak</x-slot>

@push('styles')
    @vite('resources/css/inventoryadmin.css')
    <style>
        /* ── Broken page extras ─────────────────────────── */
        .br-page-hdr {
            display:flex; align-items:center; justify-content:space-between;
            flex-wrap:wrap; gap:12px; margin-bottom:20px;
        }
        .br-page-title {
            font-family:'Outfit',sans-serif; font-size:22px; font-weight:800;
            color:#991b1b; display:flex; align-items:center; gap:10px;
        }
        .br-page-title svg { color:#dc2626; }
        .br-page-sub { font-size:13px; color:var(--muted); margin-top:3px; }

        .br-back-btn {
            display:inline-flex; align-items:center; gap:6px;
            padding:8px 16px; border-radius:10px; font-size:13px; font-weight:600;
            background:var(--white); color:var(--sub);
            border:1.5px solid var(--border); text-decoration:none;
            transition:all .18s;
        }
        .br-back-btn:hover { border-color:var(--g7); color:var(--g8); background:#eaf4ee; }

        /* ── Summary cards ──────────────────────────────── */
        .br-summary { display:grid; grid-template-columns:repeat(4,1fr); gap:13px; margin-bottom:20px; }
        @media(max-width:900px) { .br-summary { grid-template-columns:repeat(2,1fr); } }
        @media(max-width:480px) { .br-summary { grid-template-columns:1fr 1fr; } }

        .br-card {
            background:var(--white); border-radius:var(--r);
            padding:18px 20px; border:1px solid var(--border);
            box-shadow:var(--shadow); position:relative; overflow:hidden;
        }
        .br-card::before {
            content:''; position:absolute; top:0; left:0; right:0; height:3px;
            border-radius:var(--r) var(--r) 0 0;
        }
        .br-card.c-broken::before  { background:#dc2626; }
        .br-card.c-items::before   { background:#d97706; }
        .br-card.c-labs::before    { background:#7c3aed; }
        .br-card.c-cost::before    { background:#0284c7; }
        .br-card-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:16px; margin-bottom:12px; }
        .c-broken .br-card-icon  { background:#fef2f2; }
        .c-items  .br-card-icon  { background:#fffbeb; }
        .c-labs   .br-card-icon  { background:#faf5ff; }
        .c-cost   .br-card-icon  { background:#e0f2fe; }
        .br-card-lbl { font-size:10px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.09em; margin-bottom:4px; }
        .br-card-val { font-family:'Outfit',sans-serif; font-size:30px; font-weight:800; line-height:1; }
        .c-broken .br-card-val { color:#dc2626; }
        .c-items  .br-card-val  { color:#d97706; }
        .c-labs   .br-card-val  { color:#7c3aed; }
        .c-cost   .br-card-val  { color:#0284c7; }

        /* ── Lab breakdown pills ────────────────────────── */
        .br-lab-pills { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:18px; }
        .br-lab-pill {
            display:inline-flex; align-items:center; gap:6px;
            padding:6px 13px; border-radius:999px; font-size:12px; font-weight:700;
            background:#fef2f2; color:#991b1b; border:1.5px solid #fecaca;
            text-decoration:none; transition:all .18s;
        }
        .br-lab-pill:hover { background:#fee2e2; border-color:#f87171; }
        .br-lab-pill-count {
            background:#dc2626; color:#fff; border-radius:999px;
            padding:0 7px; font-size:10px; min-width:20px; text-align:center;
        }
        .br-lab-pill.all {
            background:var(--white); color:var(--g8); border-color:var(--border);
        }
        .br-lab-pill.all:hover { border-color:var(--g7); background:#eaf4ee; }
        .br-lab-pill.active { background:#991b1b; color:#fff; border-color:#991b1b; }
        .br-lab-pill.active .br-lab-pill-count { background:rgba(255,255,255,.25); }

        /* ── Filter bar ─────────────────────────────────── */
        .br-filter {
            background:var(--white); border-radius:var(--r);
            padding:12px 16px; border:1px solid var(--border);
            box-shadow:var(--shadow); margin-bottom:16px;
            display:flex; flex-wrap:wrap; gap:10px; align-items:center;
        }
        .br-search-wrap { flex:1; min-width:200px; position:relative; }
        .br-search-inp {
            width:100%; padding:8px 12px 8px 36px;
            border:1.5px solid var(--border); border-radius:10px;
            font-size:13px; font-family:inherit;
            background:var(--bg); color:var(--text); outline:none;
            transition:border-color .15s, box-shadow .15s;
        }
        .br-search-inp:focus { border-color:var(--acc2); box-shadow:0 0 0 3px rgba(142,200,224,.15); background:var(--white); }
        .br-search-icon { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:var(--muted); pointer-events:none; }

        /* ── Empty state ────────────────────────────────── */
        .br-empty {
            background:var(--white); border-radius:var(--r);
            border:1px solid var(--border); padding:64px 24px;
            text-align:center; box-shadow:var(--shadow);
        }
        .br-empty-icon { font-size:52px; margin-bottom:14px; }
        .br-empty-title { font-family:'Outfit',sans-serif; font-size:18px; font-weight:700; color:var(--g9); margin-bottom:6px; }
        .br-empty-sub { font-size:13px; color:var(--muted); }

        /* ── Broken items list ──────────────────────────── */
        .br-section { margin-bottom:24px; }
        .br-section-hdr {
            display:flex; align-items:center; gap:10px;
            padding:12px 18px;
            background:linear-gradient(135deg, #7f1d1d, #991b1b);
            border-radius:var(--r) var(--r) 0 0;
        }
        .br-section-lab { font-family:'Outfit',sans-serif; font-size:14px; font-weight:700; color:#fff; }
        .br-section-badge {
            background:rgba(255,255,255,.2); color:#fca5a5;
            border-radius:999px; padding:2px 10px; font-size:11px; font-weight:700;
        }
        .br-section-units {
            margin-left:auto; font-size:11px; color:rgba(252,165,165,.7); font-weight:600;
        }

        .br-item {
            background:var(--white); border:1px solid #fecaca;
            border-top:none; padding:16px 18px;
            transition:background .15s;
        }
        .br-item:last-child { border-radius:0 0 var(--r) var(--r); }
        .br-item:hover { background:#fff8f8; }

        .br-item-top { display:flex; align-items:flex-start; gap:14px; }
        .br-item-icon {
            width:40px; height:40px; border-radius:10px; flex-shrink:0;
            background:#fef2f2; border:1.5px solid #fecaca;
            display:flex; align-items:center; justify-content:center;
            font-size:18px;
        }
        .br-item-info { flex:1; min-width:0; }
        .br-item-name { font-weight:700; font-size:14px; color:var(--g9); }
        .br-item-meta { display:flex; flex-wrap:wrap; gap:6px; margin-top:5px; }
        .br-item-badge { display:inline-flex; align-items:center; gap:4px; font-size:10px; font-weight:600; padding:2px 8px; border-radius:999px; }
        .br-item-brand { font-size:12px; color:var(--muted); margin-top:3px; }

        /* Qty display */
        .br-qty-row { display:flex; gap:12px; margin-top:12px; }
        .br-qty-box {
            flex:1; background:var(--bg); border-radius:10px;
            border:1px solid var(--border); padding:10px; text-align:center;
        }
        .br-qty-val { font-family:'Outfit',sans-serif; font-size:20px; font-weight:800; line-height:1; }
        .br-qty-lbl { font-size:9px; font-weight:600; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin-top:3px; }
        .br-qty-total   .br-qty-val { color:var(--g9); }
        .br-qty-good    .br-qty-val { color:#16a34a; }
        .br-qty-broken  .br-qty-val { color:#dc2626; }
        .br-qty-backup  .br-qty-val { color:#2563eb; }

        /* Progress bar */
        .br-progress { margin-top:10px; }
        .br-progress-track { height:6px; background:#fee2e2; border-radius:999px; overflow:hidden; }
        .br-progress-good  { height:100%; border-radius:999px; background:linear-gradient(90deg,#16a34a,#22c55e); transition:width .5s; }
        .br-progress-meta  { display:flex; justify-content:space-between; font-size:10px; color:var(--muted); margin-top:3px; }

        /* Riwayat perbaikan */
        .br-history { margin-top:14px; }
        .br-history-title { font-size:10px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:7px; }
        .br-history-item { display:flex; align-items:center; gap:8px; padding:5px 0; border-bottom:1px solid #fef2f2; font-size:11px; }
        .br-history-item:last-child { border-bottom:none; }
        .br-history-date { color:var(--muted); white-space:nowrap; flex-shrink:0; min-width:72px; }
        .br-history-type { font-weight:600; color:var(--text); }
        .br-history-status { margin-left:auto; flex-shrink:0; }
        .br-history-empty { font-size:11px; color:var(--muted); font-style:italic; }

        /* ── Fix form (accordion) ───────────────────────── */
        .br-fix-toggle {
            width:100%; margin-top:14px;
            padding:9px 14px; border-radius:9px;
            background:#fef2f2; color:#dc2626;
            border:1.5px solid #fecaca; font-size:12px; font-weight:700;
            font-family:inherit; cursor:pointer;
            display:flex; align-items:center; justify-content:center; gap:7px;
            transition:all .18s;
        }
        .br-fix-toggle:hover { background:#fee2e2; border-color:#f87171; }
        .br-fix-toggle.open  { background:#dc2626; color:#fff; border-color:#dc2626; }
        .br-fix-toggle svg   { transition:transform .2s; }
        .br-fix-toggle.open svg { transform:rotate(180deg); }

        .br-fix-form {
            display:none; margin-top:12px; padding:16px;
            background:#fafafa; border-radius:11px; border:1px solid #fecaca;
            animation:ia-mup .2s cubic-bezier(.16,1,.3,1);
        }
        .br-fix-form.open { display:block; }

        .br-fix-row { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:12px; }
        @media(max-width:600px) { .br-fix-row { grid-template-columns:1fr; } }
        .br-fix-btn {
            width:100%; padding:10px; border-radius:10px; border:none;
            background:linear-gradient(135deg,#991b1b,#dc2626);
            color:#fff; font-size:13px; font-weight:700;
            font-family:inherit; cursor:pointer; transition:all .18s;
            display:flex; align-items:center; justify-content:center; gap:7px;
        }
        .br-fix-btn:hover { opacity:.9; transform:translateY(-1px); box-shadow:0 4px 14px rgba(185,28,28,.3); }
        .br-fix-btn:disabled { opacity:.5; cursor:not-allowed; transform:none; box-shadow:none; }

        /* ── Flash ──────────────────────────────────────── */
        .br-flash {
            display:flex; align-items:center; gap:8px;
            padding:11px 16px; border-radius:10px; margin-bottom:16px;
            font-size:13px; font-weight:600;
        }
        .br-flash.ok  { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
        .br-flash.err { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
        .br-flash svg { flex-shrink:0; }
    </style>
@endpush

@push('scripts')
<script>
function toggleFixForm(btn, itemId) {
    const form = document.getElementById('fix-form-' + itemId);
    const isOpen = form.classList.contains('open');
    // Tutup semua form lain dulu
    document.querySelectorAll('.br-fix-form.open').forEach(f => {
        f.classList.remove('open');
        f.previousElementSibling.classList.remove('open');
    });
    if (!isOpen) {
        form.classList.add('open');
        btn.classList.add('open');
        form.querySelector('input[name="fix_quantity"]').focus();
    }
}

// Filter lab via URL
function filterLab(resourceId) {
    const url = new URL(window.location.href);
    if (resourceId) {
        url.searchParams.set('resource_id', resourceId);
    } else {
        url.searchParams.delete('resource_id');
    }
    window.location.href = url.toString();
}

// Auto-dismiss flash
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.br-flash').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 420);
        }, 5000);
    });
});
</script>
@endpush

@php
    $categories = $categories ?? [];
    $catIcons = [
        'computer'   => '🖥',
        'peripheral' => '⌨',
        'furniture'  => '🪑',
        'network'    => '🌐',
        'software'   => '💿',
        'other'      => '📦',
    ];
    $activeResourceId = request('resource_id');
@endphp

{{-- ── Page header ── --}}
<div class="br-page-hdr">
    <div>
        <div class="br-page-title">
            <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
            </svg>
            Barang Rusak
        </div>
        <div class="br-page-sub">Semua barang dengan unit rusak > 0 dari seluruh laboratorium</div>
    </div>
    <div style="display:flex;gap:8px;align-items:center">
        <a href="{{ route('inventory.maintenance.index') }}" class="br-back-btn">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
            </svg>
            Log Perbaikan
        </a>
        <a href="{{ route('inventory.admin') }}" class="br-back-btn">
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
            </svg>
            Semua Inventaris
        </a>
    </div>
</div>

{{-- ── Flash messages ── --}}
@if(session('success'))
<div class="br-flash ok">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
    </svg>
    {{ session('success') }}
</div>
@endif
@if(session('error') || $errors->has('error'))
<div class="br-flash err">
    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
    </svg>
    {{ session('error') ?? $errors->first('error') }}
</div>
@endif

{{-- ── Summary cards ── --}}
<div class="br-summary">
    <div class="br-card c-broken">
        <div class="br-card-icon">⚠️</div>
        <div class="br-card-lbl">Total Unit Rusak</div>
        <div class="br-card-val">{{ $totalBrokenUnits }}</div>
    </div>
    <div class="br-card c-items">
        <div class="br-card-icon">📦</div>
        <div class="br-card-lbl">Jenis Barang</div>
        <div class="br-card-val">{{ $totalItems }}</div>
    </div>
    <div class="br-card c-labs">
        <div class="br-card-icon">🏫</div>
        <div class="br-card-lbl">Lab Terdampak</div>
        <div class="br-card-val">{{ $byLab->count() }}</div>
    </div>
    <div class="br-card c-cost">
        <div class="br-card-icon">🔧</div>
        <div class="br-card-lbl">Sudah Diperbaiki</div>
        @php
            $alreadyFixed = $brokenItems->sum(fn($i) =>
                $i->maintenanceLogs->where('status', 'Selesai')->sum('fix_quantity') ?? 0
            );
        @endphp
        <div class="br-card-val">{{ $alreadyFixed }}</div>
    </div>
</div>

{{-- ── Lab filter pills ── --}}
<div class="br-lab-pills">
    <a href="{{ route('inventory.broken', request()->except('resource_id')) }}"
       class="br-lab-pill all {{ !$activeResourceId ? 'active' : '' }}">
        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
        Semua Lab
    </a>
    @foreach($byLab as $labId => $lab)
    <a href="{{ route('inventory.broken', array_merge(request()->all(), ['resource_id' => $labId])) }}"
       class="br-lab-pill {{ $activeResourceId == $labId ? 'active' : '' }}">
        {{ $lab['name'] }}
        <span class="br-lab-pill-count">{{ $lab['units'] }}</span>
    </a>
    @endforeach
</div>

{{-- ── Search & filter bar ── --}}
<form method="GET" action="{{ route('inventory.broken') }}" class="br-filter">
    @if(request('resource_id'))
    <input type="hidden" name="resource_id" value="{{ request('resource_id') }}">
    @endif

    <div class="br-search-wrap">
        <span class="br-search-icon">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </span>
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama barang, merk, model..." class="br-search-inp">
    </div>

    <select name="category" class="ia-select">
        <option value="">Semua Kategori</option>
        @foreach($categories as $k => $v)
        <option value="{{ $k }}" {{ request('category') === $k ? 'selected' : '' }}>{{ $v }}</option>
        @endforeach
    </select>

    <button type="submit" class="ia-btn ia-btn-filter">Filter</button>

    @if(request()->hasAny(['search','category','resource_id']))
    <a href="{{ route('inventory.broken') }}" class="ia-btn-reset">× Reset</a>
    @endif
</form>


{{-- ── Empty state ── --}}
@if($brokenItems->isEmpty())
<div class="br-empty">
    <div class="br-empty-icon">✅</div>
    <div class="br-empty-title">Tidak Ada Barang Rusak</div>
    <div class="br-empty-sub">
        @if(request()->hasAny(['search','category','resource_id']))
            Tidak ada hasil untuk filter yang dipilih. <a href="{{ route('inventory.broken') }}" style="color:var(--g8)">Reset filter</a>
        @else
            Semua inventaris dalam kondisi baik. Bagus!
        @endif
    </div>
</div>

@else

{{-- ── Group by lab ── --}}
@php
    $grouped = $brokenItems->groupBy('resource_id');
@endphp

@foreach($grouped as $labId => $items)
@php $labName = $items->first()->resource->name ?? 'Lab Tidak Diketahui'; @endphp

<div class="br-section">

    {{-- Section header --}}
    <div class="br-section-hdr">
        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="color:#fca5a5;flex-shrink:0">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
        <span class="br-section-lab">{{ $labName }}</span>
        <span class="br-section-badge">{{ $items->count() }} jenis</span>
        <span class="br-section-units">{{ $items->sum('quantity_broken') }} unit rusak</span>
    </div>

    {{-- Items --}}
    @foreach($items as $item)
    @php
        $goodPct  = $item->quantity > 0 ? round($item->quantity_good / $item->quantity * 100) : 0;
        $catIcon  = $catIcons[$item->category] ?? '📦';
        $hasLogs  = $item->maintenanceLogs->isNotEmpty();
        $hasErrors = $errors->hasBag('default') && old('_item_id') == $item->id;
    @endphp

    <div class="br-item" id="item-{{ $item->id }}">
        <div class="br-item-top">

            {{-- Icon --}}
            <div class="br-item-icon">{{ $catIcon }}</div>

            {{-- Info --}}
            <div class="br-item-info">
                <div class="br-item-name">{{ $item->item_name }}</div>
                <div class="br-item-meta">
                    <span class="br-item-badge ia-badge cat-{{ $item->category }}">
                        {{ $categories[$item->category] ?? $item->category }}
                    </span>
                    <span class="br-item-badge ia-badge cond-{{ $item->condition }}">
                        {{ ['excellent'=>'Sangat Baik','good'=>'Baik','fair'=>'Cukup','poor'=>'Buruk','broken'=>'Rusak'][$item->condition] ?? $item->condition }}
                    </span>
                    @if($item->brand)
                    <span style="font-size:11px;color:var(--muted)">{{ $item->brand }}{{ $item->model ? ' · '.$item->model : '' }}</span>
                    @endif
                </div>
                @if($item->specifications)
                <div class="br-item-brand" style="margin-top:4px;font-size:11px;color:var(--muted)">{{ Str::limit($item->specifications, 80) }}</div>
                @endif
            </div>

            {{-- Broken count badge --}}
            <div style="flex-shrink:0;text-align:center;background:#fef2f2;border:2px solid #fecaca;border-radius:12px;padding:8px 14px;">
                <div style="font-family:'Outfit',sans-serif;font-size:22px;font-weight:800;color:#dc2626;line-height:1">{{ $item->quantity_broken }}</div>
                <div style="font-size:9px;font-weight:700;color:#f87171;text-transform:uppercase;letter-spacing:.06em;margin-top:2px">Rusak</div>
            </div>
        </div>

        {{-- Qty breakdown --}}
        <div class="br-qty-row">
            <div class="br-qty-box br-qty-total">
                <div class="br-qty-val">{{ $item->quantity }}</div>
                <div class="br-qty-lbl">Total</div>
            </div>
            <div class="br-qty-box br-qty-good">
                <div class="br-qty-val">{{ $item->quantity_good }}</div>
                <div class="br-qty-lbl">Baik</div>
            </div>
            <div class="br-qty-box br-qty-broken">
                <div class="br-qty-val">{{ $item->quantity_broken }}</div>
                <div class="br-qty-lbl">Rusak</div>
            </div>
            <div class="br-qty-box br-qty-backup">
                <div class="br-qty-val">{{ $item->quantity_backup }}</div>
                <div class="br-qty-lbl">Cadangan</div>
            </div>
        </div>

        {{-- Progress bar kondisi baik --}}
        <div class="br-progress">
            <div class="br-progress-track">
                <div class="br-progress-good" style="width:{{ $goodPct }}%"></div>
            </div>
            <div class="br-progress-meta">
                <span>{{ $goodPct }}% unit dalam kondisi baik</span>
                <span>{{ $item->quantity_good }}/{{ $item->quantity }}</span>
            </div>
        </div>

        {{-- Riwayat perbaikan terakhir --}}
        @if($hasLogs)
        <div class="br-history">
            <div class="br-history-title">Riwayat perbaikan terakhir</div>
            @foreach($item->maintenanceLogs as $log)
            <div class="br-history-item">
                <span class="br-history-date">{{ $log->maintenance_date->format('d M Y') }}</span>
                <span class="br-history-type">{{ $log->maintenance_type }}</span>
                @if($log->fix_quantity)
                <span style="font-size:10px;color:#16a34a;background:#f0fdf4;padding:1px 7px;border-radius:999px;font-weight:700">+{{ $log->fix_quantity }} diperbaiki</span>
                @endif
                <span class="br-history-status">
                    <span style="font-size:10px;padding:1px 7px;border-radius:999px;font-weight:700;background:{{ $log->status==='Selesai' ? '#f0fdf4' : '#fffbeb' }};color:{{ $log->status==='Selesai' ? '#15803d' : '#b45309' }}">{{ $log->status }}</span>
                </span>
            </div>
            @endforeach
        </div>
        @else
        <div class="br-history">
            <span class="br-history-empty">Belum ada riwayat perbaikan</span>
        </div>
        @endif

        {{-- Error message untuk form ini --}}
        @if($hasErrors)
        <div class="br-flash err" style="margin-top:10px;margin-bottom:0">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ $errors->first('fix_quantity') ?? $errors->first('description') }}
        </div>
        @endif

        {{-- Toggle button form perbaikan --}}
        <button type="button"
                class="br-fix-toggle {{ $hasErrors ? 'open' : '' }}"
                onclick="toggleFixForm(this, {{ $item->id }})"
                id="btn-fix-{{ $item->id }}">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            Catat Perbaikan
            <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        {{-- Form perbaikan (accordion) --}}
        <div class="br-fix-form {{ $hasErrors ? 'open' : '' }}" id="fix-form-{{ $item->id }}">
            <form method="POST" action="{{ route('inventory.mark-fixed', $item->id) }}">
                @csrf
                <input type="hidden" name="_item_id" value="{{ $item->id }}">

                <div class="br-fix-row">
                    <div>
                        <label class="ia-lbl">Jumlah Diperbaiki <span style="color:#dc2626">*</span></label>
                        <input type="number" name="fix_quantity"
                               class="ia-inp" min="1" max="{{ $item->quantity_broken }}"
                               value="{{ old('fix_quantity', 1) }}"
                               placeholder="Maks {{ $item->quantity_broken }}" required>
                        <div style="font-size:10px;color:var(--muted);margin-top:3px">Maks {{ $item->quantity_broken }} unit (jumlah rusak saat ini)</div>
                    </div>
                    <div>
                        <label class="ia-lbl">Jenis Perbaikan <span style="color:#dc2626">*</span></label>
                        <select name="maintenance_type" class="ia-inp" required>
                            <option value="Perbaikan" {{ old('maintenance_type')=='Perbaikan'?'selected':'' }}>Perbaikan</option>
                            <option value="Penggantian Komponen" {{ old('maintenance_type')=='Penggantian Komponen'?'selected':'' }}>Penggantian Komponen</option>
                            <option value="Penggantian Unit" {{ old('maintenance_type')=='Penggantian Unit'?'selected':'' }}>Penggantian Unit</option>
                            <option value="Perawatan Rutin" {{ old('maintenance_type')=='Perawatan Rutin'?'selected':'' }}>Perawatan Rutin</option>
                            <option value="Kalibrasi" {{ old('maintenance_type')=='Kalibrasi'?'selected':'' }}>Kalibrasi</option>
                            <option value="Lainnya" {{ old('maintenance_type')=='Lainnya'?'selected':'' }}>Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="br-fix-row">
                    <div>
                        <label class="ia-lbl">Biaya Perbaikan</label>
                        <input type="number" name="cost" class="ia-inp" min="0" step="1000"
                               value="{{ old('cost', 0) }}" placeholder="0">
                    </div>
                    <div style="grid-column:1/-1">
                        <label class="ia-lbl">Deskripsi Perbaikan <span style="color:#dc2626">*</span></label>
                        <textarea name="description" class="ia-inp" rows="2"
                                  placeholder="Contoh: Ganti RAM 4GB, kipas dibersihkan..."
                                  required style="resize:vertical">{{ old('description') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="br-fix-btn">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan & Pindahkan ke Baik
                </button>
            </form>
        </div>

    </div>{{-- /.br-item --}}
    @endforeach

</div>{{-- /.br-section --}}
@endforeach

@endif {{-- end if brokenItems empty --}}

</x-app-layout>
