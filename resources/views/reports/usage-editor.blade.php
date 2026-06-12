<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Laporan Penggunaan Lab</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root { --p: #003d24; --s: #B9D9EB; }
        * { box-sizing: border-box; }
        body { background: #525659; font-family: 'DM Sans', sans-serif; margin: 0; padding: 0; }

        /* ── Toolbar ────────────────────────────────── */
        .toolbar { 
            background: #323639; color: white; padding: 10px 20px; 
            display: flex; justify-content: space-between; align-items: center;
            position: sticky; top: 0; z-index: 100; box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .toolbar-title { font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 14px; }
        .btn-print { 
            background: var(--p); color: white; border: none; padding: 8px 20px; 
            border-radius: 6px; cursor: pointer; font-weight: 700; font-size: 13px;
            display: flex; align-items: center; gap: 8px; transition: 0.2s;
        }
        .btn-print:hover { background: #005c36; transform: translateY(-1px); }
        .btn-back {
            background: #64748b; color: white; border: none; padding: 8px 16px;
            border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px;
            display: flex; align-items: center; gap: 6px; text-decoration: none; transition: 0.2s;
        }
        .btn-back:hover { background: #475569; transform: translateY(-1px); }

        /* ── A4 Page ────────────────────────────────── */
        .page {
            width: 210mm; min-height: 297mm; padding: 20mm;
            margin: 30px auto; background: white; box-shadow: 0 0 20px rgba(0,0,0,0.5);
            position: relative;
        }

        /* ── Kop Surat ──────────────────────────────── */
        .kop { display: flex; align-items: center; border-bottom: 3px solid #000; padding-bottom: 15px; margin-bottom: 30px; }
        .kop-logo { width: 80px; height: 80px; margin-right: 20px; }
        .kop-text { flex: 1; text-align: center; }
        .kop-h1 { font-size: 22px; font-weight: 800; margin: 0; text-transform: uppercase; font-family: 'Outfit', sans-serif; }
        .kop-h2 { font-size: 18px; font-weight: 700; margin: 5px 0; color: var(--p); }
        .kop-p { font-size: 12px; margin: 0; color: #666; font-style: italic; }

        /* ── Content ────────────────────────────────── */
        .report-title { text-align: center; font-size: 18px; font-weight: 800; text-decoration: underline; margin-bottom: 25px; text-transform: uppercase; }
        
        .info-table { width: 100%; margin-bottom: 20px; font-size: 13px; }
        .info-table td { padding: 3px 0; }

        .main-table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 15px; }
        .main-table th { background: #f0f4f8; border: 1px solid #000; padding: 10px 8px; font-weight: 800; text-align: left; }
        .main-table td { border: 1px solid #000; padding: 6px 8px; vertical-align: top; }
        .tc { text-align: center; }
        .tr-used { background: #e6fffa; }

        .summary-box { border: 1px solid #000; padding: 15px; margin-bottom: 25px; background: #f8fafc; }
        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; text-align: center; }
        .summary-item .num { font-size: 28px; font-weight: 800; color: var(--p); }
        .summary-item .label { font-size: 12px; color: #666; text-transform: uppercase; margin-top: 5px; }

        /* ── Tanda Tangan ───────────────────────────── */
        .footer { margin-top: 40px; display: flex; justify-content: flex-end; }
        .ttd-box { width: 250px; text-align: center; }
        .ttd-space { height: 80px; }
        .ttd-name { font-weight: 800; text-decoration: underline; margin-bottom: 5px; }

        /* ── Editable Styles ────────────────────────── */
        [contenteditable="true"]:hover { background: #fffde7; outline: 1px dashed var(--p); cursor: text; }
        [contenteditable="true"]:focus { background: #fffde7; outline: 2px solid var(--p); }

        /* ── Print Styles ───────────────────────────── */
        @media print {
            body { background: white; }
            .toolbar { display: none; }
            .page { margin: 0; box-shadow: none; width: 100%; padding: 0; }
            [contenteditable="true"]:hover { outline: none; background: none; }
            @page { size: A4; margin: 15mm; }
        }
    </style>
</head>
<body>

    <div class="toolbar">
        <a href="{{ route('reports.usage.index') }}" class="btn-back">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <div class="toolbar-title">📝 Editor Laporan Penggunaan Lab (Klik teks untuk mengedit)</div>
        <button class="btn-print" onclick="window.print()">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Laporan / PDF
        </button>
    </div>

    <div class="page">
        {{-- KOP SURAT --}}
        <div class="kop">
            <img src="/assets/img/logo.png" onerror="this.src='https://via.placeholder.com/80?text=LOGO'" class="kop-logo">
            <div class="kop-text">
                <div class="kop-h1" contenteditable="true">Yayasan Pondok Pesantren Nuris Jember</div>
                <div class="kop-h2" contenteditable="true">Sistem Manajemen Laboratorium Terpadu</div>
                <div class="kop-p" contenteditable="true">Jl. Pangandaran No. 48, Antirogo, Kec. Sumbersari, Kabupaten Jember, Jawa Timur 68125</div>
            </div>
        </div>

        {{-- JUDUL --}}
        <div class="report-title" contenteditable="true">Laporan Penggunaan Laboratorium</div>

        {{-- INFO --}}
        <table class="info-table">
            <tr>
                <td style="width: 120px;">Periode</td>
                <td>: <strong contenteditable="true">{{ $month->translatedFormat('F Y') }}</strong></td>
            </tr>
            <tr>
                <td>Perhitungan</td>
                <td>: <span contenteditable="true">{{ $includeSunday ? 'Dengan Hari Minggu' : 'Tanpa Hari Minggu' }}</span></td>
            </tr>
        </table>

        @foreach($reportData as $labData)
            {{-- LAB NAME --}}
            <div style="font-size: 14px; font-weight: 800; margin: 20px 0 10px; color: var(--p);" contenteditable="true">
                {{ $labData['lab']->name }}
            </div>

            {{-- SUMMARY --}}
            <div class="summary-box">
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="num" contenteditable="true">{{ $labData['summary']['total_days'] }}</div>
                        <div class="label">Hari Kerja</div>
                    </div>
                    <div class="summary-item">
                        <div class="num" contenteditable="true">{{ $labData['summary']['used_days'] }}</div>
                        <div class="label">Digunakan</div>
                    </div>
                    <div class="summary-item">
                        <div class="num" contenteditable="true">{{ $labData['summary']['unused_days'] }}</div>
                        <div class="label">Tidak Digunakan</div>
                    </div>
                    <div class="summary-item">
                        <div class="num" contenteditable="true">{{ $labData['summary']['percentage'] }}%</div>
                        <div class="label">Persentase</div>
                    </div>
                </div>
            </div>

            {{-- TABLE --}}
            <table class="main-table">
                <thead>
                    <tr>
                        <th class="tc" style="width: 40px;">No</th>
                        <th style="width: 100px;">Tanggal</th>
                        <th>Hari</th>
                        <th>Status Penggunaan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($labData['days'] as $index => $day)
                    <tr class="{{ $day['is_used'] ? 'tr-used' : '' }}">
                        <td class="tc">{{ $index + 1 }}</td>
                        <td contenteditable="true">{{ $day['date']->translatedFormat('d') }}</td>
                        <td contenteditable="true">{{ $day['day_name'] }}</td>
                        <td class="tc" contenteditable="true">
                            {{ $day['is_used'] ? '✓ Digunakan' : 'Tidak Digunakan' }}
                        </td>
                        <td contenteditable="true">
                            @if($day['has_schedule'])
                                Jadwal Rutin
                            @endif
                            @foreach($day['bookings'] as $booking)
                                {{ $loop->first && $day['has_schedule'] ? ', ' : '' }}
                                Booking: {{ $booking->teacher_name }}
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach

        {{-- TANDA TANGAN --}}
        <div class="footer">
            <div class="ttd-box">
                <div contenteditable="true">Jember, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div contenteditable="true" style="margin-top: 5px;">Mengetahui,</div>
                <div class="ttd-space"></div>
                <div class="ttd-name" contenteditable="true">{{ auth()->user()->full_name }}</div>
                <div contenteditable="true">NIP/NIY. ...........................</div>
            </div>
        </div>
    </div>

</body>
</html>
