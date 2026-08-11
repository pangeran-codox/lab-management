<!-- x-report-editor — component editor laporan universal. Dokumentasi & contoh pemakaian ada di file terpisah: resources/views/components/report-editor-USAGE.md (bukan .blade.php, jadi tidak pernah ikut ter-compile). -->




@props([
    // Meta halaman
    'pageTitle'       => 'Editor Laporan',
    'reportTitle'     => 'Laporan',
    'backUrl'         => null,
    'headerHint'      => null,

    // Kop surat
    'logo'            => null,
    'siteName'        => '',
    'siteAddress'     => '',
    'sitePhone'       => '',
    'kopNameSize'     => 20,
    'kopAddressSize'  => 13,
    'kopPhoneSize'    => 12,

    // Tanda tangan & footer
    'siteHeadName'    => '',
    'signatureCity'   => 'Jember',
    'signatureDate'   => null,
    'reportFooter'    => '',

    // Ukuran kertas default (belum dipersist ke DB — lihat catatan di atas)
    'paperSize'       => 'A4 portrait',
    'paperSizeLabel'  => 'A4 Portrait',
])

@php
    // Fallback nama penandatangan yang aman untuk halaman publik (guest).
    // Ini menggantikan pola `auth()->user()->full_name` yang rawan error
    // saat belum login — sekarang cukup ditulis SEKALI di sini.
    $resolvedSignatureName = $siteHeadName ?: (optional(auth()->user())->full_name ?: '(Nama Pengelola)');
    $resolvedSignatureDate = $signatureDate ?: now()->translatedFormat('d F Y');

    // ID unik per instance component — jaga-jaga kalau suatu saat component
    // ini dipakai lebih dari sekali dalam satu halaman (defensif, opsional).
    // PENTING: uid ini dipakai untuk menyusun NAMA FUNGSI JavaScript
    // (window['namaFungsi_' + uid]) dan dipanggil langsung sebagai
    // identifier di atribut onclick — jadi tidak boleh mengandung
    // karakter selain huruf/angka/underscore (tanda "-" akan dibaca
    // JS sebagai operator pengurangan dan bikin syntax error).
    $uid = 'rep_' . preg_replace('/[^a-zA-Z0-9_]/', '', uniqid());
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --p: #003d24;
            --p-light: #005c36;
            --p-lighter: #e6f4ee;
            --s: #B9D9EB;
            --bg: #f0f4f8;
        }
        * { box-sizing: border-box; }
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #e3f2fd 100%);
            font-family: 'DM Sans', sans-serif;
            margin: 0;
            padding: 20px;
        }

        /* ── Toolbar ────────────────────────────────── */
        .toolbar {
            background: linear-gradient(135deg, var(--p) 0%, #004d2b 100%);
            color: white;
            padding: 14px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 8px 24px rgba(0, 61, 36, 0.25);
            border-radius: 12px;
            margin-bottom: 24px;
        }
        .toolbar-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print {
            background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
            color: white;
            border: none;
            padding: 10px 22px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 700;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.35);
        }
        .btn-print:hover {
            background: linear-gradient(135deg, #15803d 0%, #166534 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(22, 163, 74, 0.45);
        }
        .btn-back {
            text-decoration: none;
            color: white;
            background: rgba(255,255,255,0.15);
            padding: 10px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            margin-right: 15px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-back:hover {
            background: rgba(255,255,255,0.25);
            transform: translateX(-2px);
        }

        /* ── A4 Page ────────────────────────────────── */
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 20px 60px rgba(0, 61, 36, 0.15);
            position: relative;
            border-radius: 16px;
            overflow: hidden;
        }

        /* ── Mobile Responsiveness ─────────────────────────── */
        @media (max-width: 768px) {
            body { padding: 10px; }
            .toolbar { flex-wrap: wrap; gap: 12px; padding: 12px 16px; border-radius: 10px; }
            .toolbar-title { font-size: 13px; flex-basis: 100%; text-align: center; order: 3; justify-content: center; }
            .btn-back, .btn-print { font-size: 13px; padding: 10px 14px; flex: 1; justify-content: center; }
            .page { width: 100%; min-height: auto; padding: 18px; margin: 0 auto; box-shadow: 0 4px 20px rgba(0, 61, 36, 0.08); border-radius: 12px; }
            .kop { flex-direction: row; gap: 16px; }
            .kop-logo { width: 70px; height: 70px; }
            .kop-text { text-align: center; }
            .kop-h1 { font-size: 16px; }
            .kop-h2 { font-size: 13px; }
            .kop-p { font-size: 10px; }
            .summary-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .main-table, .info-table { font-size: 10px; display: block; overflow-x: auto; }
            .footer { justify-content: center; }
        }

        /* ── Kop Surat ──────────────────────────────── */
        .kop { display: flex; flex-direction: row; align-items: center; border-bottom: 3px solid var(--p); padding-bottom: 18px; margin-bottom: 32px; gap: 20px; }
        .kop-logo { width: 80px; height: 80px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; border-radius: 12px; overflow: hidden; }
        .kop-logo img { width: 100%; height: 100%; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; }
        .kop-h1 { font-size: 20px; font-weight: 800; margin: 0; text-transform: uppercase; font-family: 'Outfit', sans-serif; color: var(--p); }
        .kop-h2 { font-size: 14px; font-weight: 600; margin: 4px 0 2px; color: var(--p-light); }
        .kop-p { font-size: 12px; margin: 2px 0 0; color: #64748b; }

        /* ── Content ────────────────────────────────── */
        .report-title { text-align: center; font-size: 20px; font-weight: 800; margin-bottom: 30px; text-transform: uppercase; color: var(--p); position: relative; display: inline-block; width: 100%; }
        .report-title::after { content: ''; position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); width: 80px; height: 3px; background: linear-gradient(90deg, var(--p) 0%, var(--s) 100%); border-radius: 2px; }

        .info-table { width: 100%; margin-bottom: 28px; font-size: 13px; background: var(--p-lighter); padding: 16px 20px; border-radius: 12px; border-left: 4px solid var(--p); }
        .info-table td { padding: 4px 0; }
        .info-table td:first-child { font-weight: 700; color: var(--p); min-width: 110px; }

        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; margin-bottom: 32px; }
        .summary-card { background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border: 1px solid #bbf7d0; border-radius: 14px; padding: 18px 12px; text-align: center; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(22, 163, 74, 0.05); }
        .summary-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(22, 163, 74, 0.12); }
        .summary-val { font-size: 24px; font-weight: 800; color: var(--p); line-height: 1; }
        .summary-key { font-size: 11px; color: #64748b; margin-top: 8px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.3px; }

        .section-title { text-align: left; font-size: 15px; font-weight: 800; margin-bottom: 14px; color: var(--p); display: flex; align-items: center; gap: 8px; }
        .section-title::before { content: ''; width: 6px; height: 22px; background: linear-gradient(180deg, var(--p) 0%, var(--p-light) 100%); border-radius: 3px; }

        .main-table { width: 100%; border-collapse: collapse; font-size: 12px; margin-bottom: 30px; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .main-table thead { background: linear-gradient(135deg, var(--p) 0%, var(--p-light) 100%); }
        .main-table th { color: white; border: none; padding: 12px 10px; font-weight: 700; text-align: left; font-family: 'Outfit', sans-serif; font-size: 12px; }
        .main-table tbody tr:nth-child(even) { background: #f8faf7; }
        .main-table tbody tr:hover { background: #e6f4ee; }
        .main-table td { border: 1px solid #e2e8f0; padding: 10px 8px; vertical-align: top; }
        .tc { text-align: center; }

        /* ── Tanda Tangan ───────────────────────────── */
        .footer { margin-top: 50px; display: flex; justify-content: flex-end; }
        .ttd-box { width: 250px; text-align: center; background: #f8faf7; padding: 20px 16px; border-radius: 12px; border: 1px dashed var(--p); }
        .ttd-space { height: 90px; }
        .ttd-name { font-weight: 800; text-decoration: underline; margin-bottom: 5px; color: var(--p); }

        /* ── Editable Styles ────────────────────────── */
        [contenteditable="true"]:hover { background: #fffde7; outline: 2px dashed #f59e0b; cursor: text; border-radius: 4px; }
        [contenteditable="true"]:focus { background: #fffbeb; outline: 2px solid var(--p); border-radius: 4px; }

        /* ── Print Styles ───────────────────────────── */
        @media print {
            body { background: white; padding: 0; }
            .toolbar { display: none; }
            .page { margin: 0; box-shadow: none; width: 100%; padding: 0; border-radius: 0; }
            [contenteditable="true"]:hover, [contenteditable="true"]:focus { outline: none; background: none; }
            .summary-card { box-shadow: none; border: 1px solid #e2e8f0; }
            html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        /* ── Paper dropdown ──────────────────────────── */
        @keyframes dropIn { from { opacity:0; transform:translateY(-6px) scale(.97); } to { opacity:1; transform:none; } }
        #paper-dropdown-menu-{{ $uid }} { animation: dropIn .15s ease; }
        .paper-opt { display:flex; align-items:center; gap:8px; width:100%; padding:8px 14px; border:none; background:none; font-size:13px; font-family:'DM Sans',sans-serif; cursor:pointer; color:#1e293b; text-align:left; transition:background .1s; }
        .paper-opt:hover  { background:#f0fdf4; color:#003d24; }
        .paper-opt-active { background:#dcfce7 !important; color:#15803d !important; font-weight:700; }
        .popt-icon { display:inline-block; border:1.5px solid currentColor; border-radius:2px; flex-shrink:0; opacity:.7; }
        .popt-icon.portrait  { width:10px; height:13px; color:#0284c7; }
        .popt-icon.landscape { width:13px; height:10px; color:#7c3aed; }
    </style>
    {{-- @page dikontrol via JS agar ukuran kertas bisa diubah dari toolbar --}}
    <style id="page-style-{{ $uid }}">@page { size: {{ $paperSize }}; margin: 10mm; }</style>
</head>
<body>

    <div class="toolbar">
        @if($backUrl)
            <a href="{{ $backUrl }}" class="btn-back">
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        @endif
        <div class="toolbar-title" style="flex: 1;">
            📝 {{ $pageTitle }}
            @if($headerHint)
                <span style="font-size:12px;opacity:.7">— {{ $headerHint }}</span>
            @endif
        </div>

        {{-- Font size controls --}}
        <div style="display:flex;align-items:center;gap:6px;margin-right:12px;background:rgba(255,255,255,.1);padding:6px 10px;border-radius:8px">
            <span style="font-size:11px;color:rgba(255,255,255,.7);white-space:nowrap">Ukuran Kop:</span>
            <button type="button" onclick="repChangeSize_{{ $uid }}('kop-name-{{ $uid }}', -1)" title="Perkecil nama" style="background:rgba(255,255,255,.15);border:none;color:#fff;width:24px;height:24px;border-radius:5px;cursor:pointer;font-size:13px;font-weight:700">−</button>
            <span id="kop-name-{{ $uid }}-size-label" style="font-size:12px;color:#fff;min-width:28px;text-align:center">{{ $kopNameSize }}px</span>
            <button type="button" onclick="repChangeSize_{{ $uid }}('kop-name-{{ $uid }}', 1)" title="Perbesar nama" style="background:rgba(255,255,255,.15);border:none;color:#fff;width:24px;height:24px;border-radius:5px;cursor:pointer;font-size:13px;font-weight:700">+</button>
            <span style="color:rgba(255,255,255,.3);margin:0 2px">|</span>
            <button type="button" onclick="repChangeSize_{{ $uid }}('kop-address-{{ $uid }}', -1)" title="Perkecil alamat" style="background:rgba(255,255,255,.15);border:none;color:#fff;width:24px;height:24px;border-radius:5px;cursor:pointer;font-size:11px">−a</button>
            <span id="kop-address-{{ $uid }}-size-label" style="font-size:12px;color:#fff;min-width:28px;text-align:center">{{ $kopAddressSize }}px</span>
            <button type="button" onclick="repChangeSize_{{ $uid }}('kop-address-{{ $uid }}', 1)" title="Perbesar alamat" style="background:rgba(255,255,255,.15);border:none;color:#fff;width:24px;height:24px;border-radius:5px;cursor:pointer;font-size:11px">+a</button>
            <button type="button" id="btn-save-size-{{ $uid }}" onclick="repSaveKopSize_{{ $uid }}()" title="Simpan ukuran" style="background:#16a34a;border:none;color:#fff;padding:4px 10px;border-radius:5px;cursor:pointer;font-size:11px;font-weight:600;margin-left:4px">💾 Simpan</button>
        </div>

        {{-- Pilihan ukuran kertas --}}
        <div style="position:relative" id="paper-dropdown-wrap-{{ $uid }}">
            <button type="button" onclick="repTogglePaperDropdown_{{ $uid }}()" id="paper-dropdown-btn-{{ $uid }}"
                style="display:flex;align-items:center;gap:6px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:#fff;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span id="paper-dropdown-label-{{ $uid }}">{{ $paperSizeLabel }}</span>
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="paper-dropdown-menu-{{ $uid }}"
                style="display:none;position:absolute;top:calc(100% + 6px);right:0;background:#fff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.18);z-index:9999;min-width:170px;overflow:hidden">
                <div style="padding:6px 12px 4px;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em">Seri A</div>
                @foreach([
                    ['A4 portrait', 'A4 Portrait', 'portrait'],
                    ['A4 landscape', 'A4 Landscape', 'landscape'],
                    ['A3 portrait', 'A3 Portrait', 'portrait'],
                    ['A3 landscape', 'A3 Landscape', 'landscape'],
                    ['A5 portrait', 'A5 Portrait', 'portrait'],
                    ['A5 landscape', 'A5 Landscape', 'landscape'],
                ] as [$val, $lbl, $orient])
                <button type="button" onclick="repSelectPaperSize_{{ $uid }}('{{ $val }}','{{ $lbl }}')" class="paper-opt @if($val === $paperSize) paper-opt-active @endif" data-value="{{ $val }}">
                    <span class="popt-icon {{ $orient }}"></span>{{ $lbl }}
                </button>
                @endforeach
                <div style="height:1px;background:#f1f5f9;margin:4px 0"></div>
                <div style="padding:4px 12px 4px;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em">US</div>
                @foreach([
                    ['letter portrait', 'Letter Portrait', 'portrait'],
                    ['letter landscape', 'Letter Landscape', 'landscape'],
                    ['legal portrait', 'Legal Portrait', 'portrait'],
                    ['legal landscape', 'Legal Landscape', 'landscape'],
                ] as [$val, $lbl, $orient])
                <button type="button" onclick="repSelectPaperSize_{{ $uid }}('{{ $val }}','{{ $lbl }}')" class="paper-opt @if($val === $paperSize) paper-opt-active @endif" data-value="{{ $val }}">
                    <span class="popt-icon {{ $orient }}"></span>{{ $lbl }}
                </button>
                @endforeach
            </div>
        </div>

        <button type="button" class="btn-print" onclick="window.print()">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Laporan / PDF
        </button>
    </div>

    <div class="page">
        {{-- KOP SURAT --}}
        <div class="kop">
            <div class="kop-logo" style="display: flex; align-items: center; justify-content: center; background: var(--p); border-radius: 12px; color: white; overflow: hidden;">
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="Logo" style="width: 100%; height: 100%; object-fit: cover;">
                @else
                    <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 2 0 00-3.86.517l-.318.158a6 2 0 01-3.86.517L6.05 15.21a2 2 0 00-1.183.308l-1.063.671a1 1 0 00.441 1.811l1.157.058a6 2 0 01.872.11l2.13.426a6 2 0 003.86-.517l.318-.158a6 2 0 013.86-.517l2.387.477a2 2 0 001.022.547l.53.265a1 1 0 001.498-.894l-.193-2.321z"/>
                        <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                @endif
            </div>
            <div class="kop-text">
                <div class="kop-h1" id="kop-name-{{ $uid }}" contenteditable="true"
                    style="font-size:{{ $kopNameSize }}px">{{ $siteName }}</div>
                @if($siteAddress)
                <div class="kop-h2" id="kop-address-{{ $uid }}" contenteditable="true"
                    style="font-size:{{ $kopAddressSize }}px">{{ $siteAddress }}</div>
                @endif
                @if($sitePhone)
                <div class="kop-p" id="kop-phone-{{ $uid }}" contenteditable="true"
                    style="font-size:{{ $kopPhoneSize }}px">Telp. {{ $sitePhone }}</div>
                @endif
            </div>
        </div>

        {{-- JUDUL --}}
        <div class="report-title">{{ $reportTitle }}</div>

        {{-- INFO — slot, default kosong kalau tidak diisi caller --}}
        @isset($info)
            <table class="info-table">
                {{ $info }}
            </table>
        @endisset

        {{-- SUMMARY — slot opsional --}}
        @isset($summary)
            {{ $summary }}
        @endisset

        {{-- KONTEN UTAMA — slot default, wajib diisi caller --}}
        {{ $slot }}

        {{-- TANDA TANGAN --}}
        @isset($signature)
            {{ $signature }}
        @else
            <div class="footer">
                <div class="ttd-box">
                    <div>{{ $signatureCity }}, {{ $resolvedSignatureDate }}</div>
                    <div style="margin-top: 5px;">Mengetahui,</div>
                    <div class="ttd-space"></div>
                    <div class="ttd-name" contenteditable="true">{{ $resolvedSignatureName }}</div>
                    <div contenteditable="true">NIP/NIY. ...........................</div>
                </div>
            </div>
        @endisset

        @if($reportFooter)
        <div style="margin-top:24px;border-top:1px solid #e2e8f0;padding-top:12px;text-align:center">
            <pre style="font-family:inherit;font-size:11px;color:#64748b;margin:0;white-space:pre-wrap" contenteditable="true">{{ $reportFooter }}</pre>
        </div>
        @endif
    </div>

<script>
(function () {
    // Namespace per-instance dengan uid supaya component ini aman dipakai
    // lebih dari sekali dalam satu halaman tanpa bentrok variabel global.
    const uid = {!! json_encode($uid) !!};

    const sizes = {
        ['kop-name-' + uid]:    {{ (int) $kopNameSize }},
        ['kop-address-' + uid]: {{ (int) $kopAddressSize }},
        ['kop-phone-' + uid]:   {{ (int) $kopPhoneSize }},
    };
    const MIN = { ['kop-name-' + uid]: 10, ['kop-address-' + uid]: 8, ['kop-phone-' + uid]: 8 };
    const MAX = { ['kop-name-' + uid]: 48, ['kop-address-' + uid]: 32, ['kop-phone-' + uid]: 32 };

    window['repChangeSize_' + uid] = function (id, delta) {
        const el = document.getElementById(id);
        if (!el) return;
        sizes[id] = Math.min(MAX[id], Math.max(MIN[id], sizes[id] + delta));
        el.style.fontSize = sizes[id] + 'px';
        const label = document.getElementById(id + '-size-label');
        if (label) label.textContent = sizes[id] + 'px';
    };

    window['repSaveKopSize_' + uid] = function () {
        const btn = document.getElementById('btn-save-size-' + uid);
        if (!btn) return;
        btn.textContent = '⏳ Menyimpan...';
        btn.disabled = true;

        fetch('{{ route("settings.kop-size") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({
                kop_name_size:    sizes['kop-name-' + uid],
                kop_address_size: sizes['kop-address-' + uid],
                kop_phone_size:   sizes['kop-phone-' + uid],
            }),
        })
        .then(r => r.json())
        .then(data => {
            btn.textContent = data.success ? '✅ Tersimpan' : '❌ Gagal';
            btn.style.background = data.success ? '#16a34a' : '#dc2626';
        })
        .catch(() => {
            btn.textContent = '❌ Gagal';
            btn.style.background = '#dc2626';
        })
        .finally(() => {
            setTimeout(() => {
                btn.textContent = '💾 Simpan';
                btn.style.background = '#16a34a';
                btn.disabled = false;
            }, 2000);
        });
    };

    window['repTogglePaperDropdown_' + uid] = function () {
        const menu = document.getElementById('paper-dropdown-menu-' + uid);
        if (!menu) return;
        menu.style.display = (menu.style.display !== 'none') ? 'none' : 'block';
    };

    window['repSelectPaperSize_' + uid] = function (value, label) {
        const styleTag = document.getElementById('page-style-' + uid);
        if (styleTag) styleTag.textContent = `@page { size: ${value}; margin: 10mm; }`;
        const labelEl = document.getElementById('paper-dropdown-label-' + uid);
        if (labelEl) labelEl.textContent = label;
        document.querySelectorAll('#paper-dropdown-menu-' + uid + ' .paper-opt').forEach(btn => {
            btn.classList.toggle('paper-opt-active', btn.dataset.value === value);
        });
        const menu = document.getElementById('paper-dropdown-menu-' + uid);
        if (menu) menu.style.display = 'none';
    };

    document.addEventListener('click', function (e) {
        const wrap = document.getElementById('paper-dropdown-wrap-' + uid);
        const menu = document.getElementById('paper-dropdown-menu-' + uid);
        if (wrap && menu && !wrap.contains(e.target)) {
            menu.style.display = 'none';
        }
    });
})();
</script>

</body>
</html>