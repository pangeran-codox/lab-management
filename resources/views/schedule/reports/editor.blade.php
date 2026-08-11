<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal - {{ $resource->name }}</title>
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
            width: 297mm;
            min-height: 210mm;
            padding: 15mm;
            margin: 0 auto;
            background: white;
            box-shadow: 0 20px 60px rgba(0, 61, 36, 0.15);
            position: relative;
            border-radius: 16px;
            overflow: hidden;
        }

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
            .main-table, .info-table { font-size: 9px; display: block; overflow-x: auto; }
            .footer { justify-content: center; }
        }

        /* ── Kop Surat ──────────────────────────────── */
        .kop {
            display: flex;
            flex-direction: row;
            align-items: center;
            border-bottom: 3px solid var(--p);
            padding-bottom: 18px;
            margin-bottom: 24px;
            gap: 20px;
        }
        .kop-logo {
            width: 80px; height: 80px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            border-radius: 12px; overflow: hidden;
        }
        .kop-logo img { width: 100%; height: 100%; object-fit: contain; }
        .kop-text { flex: 1; text-align: center; }
        .kop-h1 { font-size: 20px; font-weight: 800; margin: 0; text-transform: uppercase; font-family: 'Outfit', sans-serif; color: var(--p); }
        .kop-h2 { font-size: 14px; font-weight: 600; margin: 4px 0 2px; color: var(--p-light); }
        .kop-p { font-size: 12px; margin: 2px 0 0; color: #64748b; }

        /* ── Content ────────────────────────────────── */
        .report-title {
            text-align: center; font-size: 20px; font-weight: 800; margin-bottom: 24px;
            text-transform: uppercase; color: var(--p); position: relative; display: inline-block; width: 100%;
        }
        .report-title::after {
            content: ''; position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%);
            width: 80px; height: 3px; background: linear-gradient(90deg, var(--p) 0%, var(--s) 100%); border-radius: 2px;
        }

        .info-table {
            width: 100%; margin-bottom: 20px; font-size: 13px; background: var(--p-lighter);
            padding: 16px 20px; border-radius: 12px; border-left: 4px solid var(--p);
        }
        .info-table td { padding: 4px 0; }
        .info-table td:first-child { font-weight: 700; color: var(--p); min-width: 110px; }

        .main-table {
            width: 100%; border-collapse: collapse; font-size: 10px; border-radius: 12px;
            overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .main-table thead { background: linear-gradient(135deg, var(--p) 0%, var(--p-light) 100%); }
        .main-table th {
            color: white; border: none; padding: 8px 6px; font-weight: 700; text-align: center;
            font-family: 'Outfit', sans-serif; font-size: 11px;
        }
        .main-table th.th-time { text-align: left; }
        .main-table tbody tr:nth-child(even) { background: #f8faf7; }
        .main-table td { border: 1px solid #e2e8f0; padding: 6px; vertical-align: top; }
        .td-time { font-weight: 700; color: var(--p); white-space: nowrap; }
        .break-row td { background: #fdf6e3 !important; text-align: center; font-style: italic; color: #92722a; }
        .sc-teacher { font-weight: 700; }
        .sc-class { color: #555; font-size: 9px; }
        .sc-subject { color: #888; font-size: 9px; }
        .tc { text-align: center; color: #cbd5e1; }

        /* ── Tanda Tangan ───────────────────────────── */
        .footer { margin-top: 40px; display: flex; justify-content: flex-end; }
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
            html { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }

        /* @page HARUS di top-level, tidak boleh di-nest di dalam @media print */

        /* ── Paper dropdown ──────────────────────────── */
        @keyframes dropIn { from { opacity:0; transform:translateY(-6px) scale(.97); } to { opacity:1; transform:none; } }
        #paper-dropdown-menu { animation: dropIn .15s ease; }
        .paper-opt { display:flex; align-items:center; gap:8px; width:100%; padding:8px 14px; border:none; background:none; font-size:13px; font-family:'DM Sans',sans-serif; cursor:pointer; color:#1e293b; text-align:left; transition:background .1s; }
        .paper-opt:hover  { background:#f0fdf4; color:#003d24; }
        .paper-opt-active { background:#dcfce7 !important; color:#15803d !important; font-weight:700; }
        .popt-icon { display:inline-block; border:1.5px solid currentColor; border-radius:2px; flex-shrink:0; opacity:.7; }
        .popt-icon.portrait  { width:10px; height:13px; color:#0284c7; }
        .popt-icon.landscape { width:13px; height:10px; color:#7c3aed; }
    </style>
    <style id="page-style">@page { size: A4 landscape; margin: 10mm; }</style>
</head>
<body>

    <div class="toolbar">
        <a href="javascript:history.back()" class="btn-back">← Kembali</a>
        <div class="toolbar-title" style="flex: 1;">📅 Editor Jadwal (Klik teks untuk mengedit sebelum cetak)</div>

        {{-- Pilihan ukuran kertas — custom dropdown agar konsisten di semua browser --}}
        <div style="position:relative" id="paper-dropdown-wrap">
            <button onclick="togglePaperDropdown()" id="paper-dropdown-btn"
                style="display:flex;align-items:center;gap:6px;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:#fff;border-radius:8px;padding:6px 12px;font-size:12px;font-weight:600;cursor:pointer;white-space:nowrap;font-family:inherit">
                <svg width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span id="paper-dropdown-label">A4 Landscape</span>
                <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="paper-dropdown-menu"
                style="display:none;position:absolute;top:calc(100% + 6px);right:0;background:#fff;border:1px solid #e2e8f0;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,.18);z-index:9999;min-width:170px;overflow:hidden">
                <div style="padding:6px 12px 4px;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em">Seri A</div>
                <button onclick="selectPaperSize('A4 portrait','A4 Portrait')"    class="paper-opt" data-value="A4 portrait">             <span class="popt-icon portrait"></span>A4 Portrait</button>
                <button onclick="selectPaperSize('A4 landscape','A4 Landscape')"  class="paper-opt paper-opt-active" data-value="A4 landscape"><span class="popt-icon landscape"></span>A4 Landscape</button>
                <button onclick="selectPaperSize('A3 portrait','A3 Portrait')"    class="paper-opt" data-value="A3 portrait">             <span class="popt-icon portrait"></span>A3 Portrait</button>
                <button onclick="selectPaperSize('A3 landscape','A3 Landscape')"  class="paper-opt" data-value="A3 landscape">           <span class="popt-icon landscape"></span>A3 Landscape</button>
                <button onclick="selectPaperSize('A5 portrait','A5 Portrait')"    class="paper-opt" data-value="A5 portrait">             <span class="popt-icon portrait"></span>A5 Portrait</button>
                <button onclick="selectPaperSize('A5 landscape','A5 Landscape')"  class="paper-opt" data-value="A5 landscape">           <span class="popt-icon landscape"></span>A5 Landscape</button>
                <div style="height:1px;background:#f1f5f9;margin:4px 0"></div>
                <div style="padding:4px 12px 4px;font-size:10px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:.06em">US</div>
                <button onclick="selectPaperSize('letter portrait','Letter Portrait')"   class="paper-opt" data-value="letter portrait">   <span class="popt-icon portrait"></span>Letter Portrait</button>
                <button onclick="selectPaperSize('letter landscape','Letter Landscape')" class="paper-opt" data-value="letter landscape"> <span class="popt-icon landscape"></span>Letter Landscape</button>
                <button onclick="selectPaperSize('legal portrait','Legal Portrait')"     class="paper-opt" data-value="legal portrait">     <span class="popt-icon portrait"></span>Legal Portrait</button>
                <button onclick="selectPaperSize('legal landscape','Legal Landscape')"   class="paper-opt" data-value="legal landscape">   <span class="popt-icon landscape"></span>Legal Landscape</button>
            </div>
        </div>

        <button class="btn-print" onclick="window.print()">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Jadwal / PDF
        </button>
    </div>

    <div class="page">
        {{-- KOP SURAT --}}
        <div class="kop">
            <div class="kop-logo" style="display: flex; align-items: center; justify-content: center; background: var(--p); border-radius: 12px; color: white; overflow: hidden;">
                @if($logo)
                    <img src="{{ asset('storage/' . $logo) }}" alt="Logo">
                @else
                    <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                @endif
            </div>
            <div class="kop-text">
                <div class="kop-h1" style="font-size:{{ $kopNameSize }}px">{{ $siteName }}</div>
                @if($siteAddress)
                <div class="kop-h2" style="font-size:{{ $kopAddressSize }}px">{{ $siteAddress }}</div>
                @endif
                @if($sitePhone)
                <div class="kop-p" style="font-size:{{ $kopPhoneSize }}px">Telp. {{ $sitePhone }}</div>
                @endif
            </div>
        </div>

        {{-- JUDUL --}}
        <div class="report-title">Jadwal Laboratorium — {{ $resource->name }}</div>

        {{-- INFO --}}
        <table class="info-table">
            <tr>
                <td>Laboratorium</td>
                <td>: <strong>{{ $resource->name }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Cetak</td>
                <td>: {{ $date }}</td>
            </tr>
        </table>

        {{-- TABLE JADWAL --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th class="th-time">Jam</th>
                    @foreach($days as $dayEn => $dayId)
                        <th>{{ $dayId }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($timeSlots as $slot)
                    @if($slot->is_break)
                        <tr class="break-row">
                            <td colspan="{{ count($days) + 1 }}">
                                Istirahat ·
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                @if($slot->end_time) – {{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }} @endif
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td class="td-time">
                                {{ $slot->name }}<br>
                                {{ \Carbon\Carbon::parse($slot->start_time)->format('H:i') }}
                                @if($slot->end_time)–{{ \Carbon\Carbon::parse($slot->end_time)->format('H:i') }}@endif
                            </td>
                            @foreach($days as $dayEn => $dayId)
                                @php
                                    $key = $dayEn . '_' . $slot->id;
                                    $sched = $scheduleGrid->get($key)?->first();
                                @endphp
                                <td>
                                    @if($sched)
                                        <div class="sc-teacher">{{ $sched->teacher_name }}</div>
                                        <div class="sc-class">{{ $sched->labClass?->name ?? '-' }}</div>
                                        @if($sched->subject_name)
                                            <div class="sc-subject">{{ $sched->subject_name }}</div>
                                        @endif
                                    @else
                                        <div class="tc">-</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        {{-- TANDA TANGAN --}}
        <div class="footer">
            <div class="ttd-box">
                <div>Jember, {{ $date }}</div>
                <div style="margin-top: 5px;">Mengetahui,</div>
                <div class="ttd-space"></div>
                <div class="ttd-name" contenteditable="true">{{ $siteHeadName ?: (auth()->user()->full_name ?? '(Nama Pengelola)') }}</div>
                <div contenteditable="true">NIP/NIY. ...........................</div>
            </div>
        </div>

        @if($reportFooter)
        <div style="margin-top:24px;border-top:1px solid #e2e8f0;padding-top:12px;text-align:center">
            <pre style="font-family:inherit;font-size:11px;color:#64748b;margin:0;white-space:pre-wrap" contenteditable="true">{{ $reportFooter }}</pre>
        </div>
        @endif
    </div>

<script>
function togglePaperDropdown() {
    const menu = document.getElementById('paper-dropdown-menu');
    menu.style.display = menu.style.display !== 'none' ? 'none' : 'block';
}

function selectPaperSize(value, label) {
    document.getElementById('page-style').textContent =
        '@page { size: ' + value + '; margin: 10mm; }';
    document.getElementById('paper-dropdown-label').textContent = label;
    document.querySelectorAll('.paper-opt').forEach(btn => {
        btn.classList.toggle('paper-opt-active', btn.dataset.value === value);
    });
    document.getElementById('paper-dropdown-menu').style.display = 'none';
}

document.addEventListener('click', function(e) {
    const wrap = document.getElementById('paper-dropdown-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('paper-dropdown-menu').style.display = 'none';
    }
});
</script>
</body>
</html>