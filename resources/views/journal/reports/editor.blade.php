<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jurnal Lab - {{ $date }}</title>
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
            gap: 12px;
            flex-wrap: wrap;
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
            display: inline-flex;
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
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .btn-back:hover {
            background: rgba(255,255,255,0.25);
            transform: translateX(-2px);
        }
        .toolbar-actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .date-select-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.1);
            padding: 6px 12px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.15);
        }
        .date-select-wrap label {
            font-size: 12px;
            font-weight: 600;
            color: rgba(255,255,255,0.85);
        }
        .date-select-wrap input[type=date] {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 6px 10px;
            border-radius: 8px;
            font-family: inherit;
            font-weight: 600;
            cursor: pointer;
        }
        .date-select-wrap input[type=date]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
        .btn-go {
            background: rgba(185, 217, 235, 0.2);
            color: var(--s);
            border: 1px solid rgba(185, 217, 235, 0.3);
            padding: 6px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-family: inherit;
            transition: all 0.2s;
        }
        .btn-go:hover {
            background: rgba(185, 217, 235, 0.35);
            transform: translateY(-1px);
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
            .toolbar-actions { width: 100%; flex-wrap: wrap; }
            .btn-back, .btn-print, .btn-go { font-size: 13px; padding: 10px 14px; flex: 1; justify-content: center; }
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
        .kop-logo-svg {
            width: 80px; height: 80px;
            background: var(--p);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        .kop-text { flex: 1; text-align: center; }
        .kop-h1 { font-size: 20px; font-weight: 800; margin: 0; text-transform: uppercase; font-family: 'Outfit', sans-serif; color: var(--p); }
        .kop-h2 { font-size: 14px; font-weight: 600; margin: 4px 0 2px; color: var(--p-light); }
        .kop-p { font-size: 12px; margin: 2px 0 0; color: #64748b; }

        /* ── Content ────────────────────────────────── */
        .report-title {
            text-align: center; font-size: 20px; font-weight: 800; margin-bottom: 8px;
            text-transform: uppercase; color: var(--p); position: relative; display: inline-block; width: 100%;
        }
        .report-title::after {
            content: ''; position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%);
            width: 80px; height: 3px; background: linear-gradient(90deg, var(--p) 0%, var(--s) 100%); border-radius: 2px;
        }

        .info-table {
            width: 100%; margin: 24px auto 20px; font-size: 13px; background: var(--p-lighter);
            padding: 16px 20px; border-radius: 12px; border-left: 4px solid var(--p);
            max-width: 400px;
        }
        .info-table td { padding: 4px 0; }
        .info-table td:first-child { font-weight: 700; color: var(--p); min-width: 110px; }

        .lab-section {
            margin-bottom: 32px;
            page-break-inside: avoid;
        }
        .lab-section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 800;
            color: white;
            background: linear-gradient(135deg, var(--p), var(--p-light));
            padding: 10px 18px;
            border-radius: 10px 10px 0 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .lab-section-title svg { width: 18px; height: 18px; color: var(--s); }

        .main-table {
            width: 100%; border-collapse: collapse; font-size: 10px; border-radius: 0 0 12px 12px;
            overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
        .main-table thead { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); }
        .main-table th {
            color: var(--p); border: none; padding: 10px 8px; font-weight: 800; text-align: left;
            font-family: 'Outfit', sans-serif; font-size: 11px;
            border-bottom: 2px solid var(--p);
        }
        .main-table th.th-time { width: 140px; }
        .main-table th.th-status { width: 100px; text-align: center; }
        .main-table tbody tr:nth-child(even) { background: #f8faf7; }
        .main-table td { border: 1px solid #e2e8f0; padding: 8px; vertical-align: top; }
        .td-time { font-weight: 700; color: var(--p); white-space: nowrap; }
        .tc-teacher { font-weight: 700; color: var(--p); font-size: 11px; }
        .tc-class { font-size: 10px; color: #555; }
        .tc-subject {
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            color: #1e40af;
            background: #dbeafe;
            border: 1px solid #bfdbfe;
            padding: 1px 6px;
            border-radius: 999px;
            margin-top: 2px;
        }
        .tc-activity { margin-top: 4px; padding-top: 4px; border-top: 1px dashed #e2e8f0; color: #374151; font-size: 10px; }
        .tc-notes { margin-top: 4px; color: #6b7280; font-size: 10px; font-style: italic; }
        .tc-photos {
            margin-top: 6px;
            display: flex;
            gap: 4px;
            flex-wrap: wrap;
        }
        .tc-photo {
            width: 50px;
            height: 40px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .tc-photo img { width: 100%; height: 100%; object-fit: cover; }
        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
        }
        .badge-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-muted { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
        .tc-status { text-align: center; }
        .empty-row {
            color: #9ca3af;
            font-style: italic;
            text-align: center;
            padding: 12px !important;
        }

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
            .lab-section { page-break-inside: avoid; }
        }

        /* @page HARUS di top-level, tidak boleh di-nest di dalam @media print */
        @page { size: A4 landscape; margin: 10mm; }
        @page :first { margin-top: 10mm; }
    </style>
</head>
<body>

    <div class="toolbar">
        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('journal.index', ['date' => $dateRaw]) }}" class="btn-back">← Kembali</a>
            <form method="GET" action="{{ route('journal.export.pdf') }}" style="display: inline-flex; gap: 8px; align-items: center; margin: 0;" onsubmit="this.querySelector('input[name=date]').value = this.querySelector('#date-picker').value;">
                <div class="date-select-wrap">
                    <label for="date-picker">Tanggal:</label>
                    <input type="date" id="date-picker" value="{{ $dateRaw }}">
                    <button type="submit" class="btn-go">Go</button>
                    <input type="hidden" name="date" value="{{ $dateRaw }}">
                </div>
            </form>
        </div>
        <div class="toolbar-title" style="flex: 1;">📔 Editor Jurnal Lab (Klik teks untuk mengedit sebelum cetak)</div>
        <div class="toolbar-actions">
            <button class="btn-print" onclick="window.print()">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Cetak Jurnal / PDF
            </button>
        </div>
    </div>

    <div class="page">
        {{-- KOP SURAT --}}
        <div class="kop">
            <div class="{{ $logo ? 'kop-logo' : 'kop-logo-svg' }}">
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
        <div class="report-title">Jurnal Kegiatan Laboratorium</div>

        {{-- INFO --}}
        <table class="info-table">
            <tr>
                <td>Tanggal</td>
                <td>: <strong contenteditable="true">{{ $date }}</strong></td>
            </tr>
        </table>

        {{-- TABEL JURNAL PER LAB --}}
        @foreach($resources as $resource)
        @php
            $rows = $resourceRows[$resource->id] ?? [];
        @endphp
        <div class="lab-section">
            <div class="lab-section-title">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                <span contenteditable="true">{{ $resource->name }}</span>
                @if($resource->capacity)
                <span style="margin-left:auto; font-size:11px; font-weight:500; opacity:.85;">Kapasitas: {{ $resource->capacity }} komputer</span>
                @endif
            </div>
            <table class="main-table">
                <thead>
                    <tr>
                        <th class="th-time">Jam</th>
                        <th>Kegiatan & Guru</th>
                        <th style="width: 130px;">Materi / Kelas</th>
                        <th>Catatan & Dokumentasi</th>
                        <th class="th-status">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        @if($row['type'] === 'empty')
                        <tr>
                            <td class="td-time">
                                <div style="font-weight:700;">{{ $row['slot_name'] }}</div>
                                <div style="font-size:9px; color:#6b7280; margin-top:1px;">{{ $row['slot_time'] }}</div>
                            </td>
                            <td class="empty-row" colspan="4">Tidak ada kegiatan</td>
                        </tr>
                        @else
                        @php
                            $group = $row['group'];
                            $journal = $group['journal'] ?? null;
                            $subjectName = $journal['subject_name'] ?? $group['subject_name'] ?? '';
                            $className = $journal['class_name'] ?? $group['class_name'] ?? '';
                            $teacherName = $journal['teacher_name'] ?? $group['teacher_name'] ?? '';
                            $activity = $journal['activity'] ?? $group['activity'] ?? '';
                            $notes = $journal['notes'] ?? '';
                            $photos = $journal['photos'] ?? [];
                            $statusClass = $journal ? 'badge-success' : 'badge-muted';
                            $statusText = $journal ? 'Sudah Diisi' : 'Belum Diisi';
                        @endphp
                        <tr>
                            <td class="td-time">
                                <div style="font-weight:700;">{{ $group['slot_name'] }}</div>
                                <div style="font-size:9px; color:#6b7280; margin-top:1px;">{{ $group['slot_time'] }}</div>
                                @if($group['slot_count'] > 1)
                                <div style="margin-top:3px;">
                                    <span class="badge badge-muted" style="font-size:9px;">{{ $group['slot_count'] }} jam</span>
                                </div>
                                @endif
                            </td>
                            <td>
                                <div class="tc-teacher" contenteditable="true">{{ $teacherName ?: '-' }}</div>
                                @if($activity)
                                <div class="tc-activity" contenteditable="true">{{ $activity }}</div>
                                @endif
                            </td>
                            <td>
                                @if($className)
                                <div class="tc-class" contenteditable="true">{{ $className }}</div>
                                @endif
                                @if($subjectName)
                                <div class="tc-subject" contenteditable="true">{{ $subjectName }}</div>
                                @endif
                                @if(!$className && !$subjectName)
                                <span style="color:#cbd5e1;">-</span>
                                @endif
                            </td>
                            <td>
                                @if($notes)
                                <div class="tc-notes" contenteditable="true">📝 {{ $notes }}</div>
                                @endif
                                @if(!empty($photos))
                                <div class="tc-photos">
                                    @foreach($photos as $idx => $photo)
                                    @if($idx < 4)
                                    <a href="{{ $photo['url'] }}" target="_blank" class="tc-photo" title="Klik untuk lihat">
                                        <img src="{{ $photo['url'] }}" alt="Dokumentasi {{ $idx+1 }}">
                                    </a>
                                    @endif
                                    @endforeach
                                    @if(count($photos) > 4)
                                    <div class="tc-photo" style="background:#f0fdf4; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:800; color:#15803d;">
                                        +{{ count($photos) - 4 }}
                                    </div>
                                    @endif
                                </div>
                                @endif
                                @if(!$notes && empty($photos))
                                <span style="color:#cbd5e1; font-size:10px;">-</span>
                                @endif
                            </td>
                            <td class="tc-status">
                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="empty-row" style="padding: 24px !important;">
                                🔍 Tidak ada data jadwal atau kegiatan untuk lab ini pada tanggal {{ $date }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endforeach

        {{-- TANDA TANGAN --}}
        <div class="footer">
            <div class="ttd-box">
                <div>Jember, <span contenteditable="true">{{ $date }}</span></div>
                <div style="margin-top: 5px;">Mengetahui,</div>
                <div class="ttd-space"></div>
                <div class="ttd-name" contenteditable="true">{{ $siteHeadName ?: (auth()->check() ? auth()->user()->full_name : '(Nama Pengelola Lab)') }}</div>
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
        // Date picker handler
        document.getElementById('date-picker').addEventListener('change', function(e) {
            const dateVal = e.target.value;
            if (dateVal) {
                const url = new URL(window.location.href);
                url.searchParams.set('date', dateVal);
                window.location.href = url.toString();
            }
        });
    </script>

</body>
</html>
