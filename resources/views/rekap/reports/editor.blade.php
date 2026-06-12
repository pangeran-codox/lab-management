<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Laporan Rekap Penggunaan Laboratorium</title>
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
            text-decoration: none; color: white; background: #64748b; padding: 8px 16px;
            border-radius: 6px; font-size: 13px; font-weight: 600; margin-right: 15px;
            transition: 0.2s;
        }
        .btn-back:hover { background: #475569; }

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

        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 25px; }
        .summary-card { background: #f8faf7; border: 1px solid #e8f0e6; border-radius: 8px; padding: 12px; text-align: center; }
        .summary-val { font-size: 20px; font-weight: 800; }
        .summary-key { font-size: 11px; color: #6b7280; margin-top: 4px; }

        .section-title { text-align: left; font-size: 14px; font-weight: 800; margin-bottom: 12px; color: var(--p); }
        
        .main-table { width: 100%; border-collapse: collapse; font-size: 11px; margin-bottom: 25px; }
        .main-table th { background: #f0f4f8; border: 1px solid #000; padding: 10px 8px; font-weight: 800; text-align: left; }
        .main-table td { border: 1px solid #000; padding: 8px; vertical-align: top; }
        .tc { text-align: center; }

        /* ── Tanda Tangan ───────────────────────────── */
        .footer { margin-top: 50px; display: flex; justify-content: flex-end; }
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
        <a href="{{ auth()->check() ? route('reports.usage.index') : route('rekap.public') }}" class="btn-back">
            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali
        </a>
        <div class="toolbar-title" style="flex: 1;">📝 Editor Laporan Rekap Penggunaan (Klik teks untuk mengedit sebelum cetak)</div>
        <button class="btn-print" onclick="window.print()">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            Cetak Laporan / PDF
        </button>
    </div>

    <div class="page">
        {{-- KOP SURAT --}}
        <div class="kop">
            <div class="kop-logo" style="display: flex; align-items: center; justify-content: center; background: var(--p); border-radius: 12px; color: white;">
                <svg width="40" height="40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 2 0 00-3.86.517l-.318.158a6 2 0 01-3.86.517L6.05 15.21a2 2 0 00-1.183.308l-1.063.671a1 1 0 00.441 1.811l1.157.058a6 2 0 01.872.11l2.13.426a6 2 0 003.86-.517l.318-.158a6 2 0 013.86-.517l2.387.477a2 2 0 001.022.547l.53.265a1 1 0 001.498-.894l-.193-2.321z"/>
                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div class="kop-text">
                <div class="kop-h1" contenteditable="true">Yayasan Pondok Pesantren Nuris Jember</div>
                <div class="kop-h2" contenteditable="true">Sistem Manajemen Laboratorium Terpadu</div>
                <div class="kop-p" contenteditable="true">Jl. Pangandaran No. 48, Antirogo, Kec. Sumbersari, Kabupaten Jember, Jawa Timur 68125</div>
            </div>
        </div>

        {{-- JUDUL --}}
        <div class="report-title" contenteditable="true">Laporan Rekap Penggunaan Laboratorium</div>

        {{-- INFO --}}
        <table class="info-table">
            <tr>
                <td style="width: 120px;">Lembaga</td>
                <td>: <strong contenteditable="true">{{ 
                    (count($labData) === 1 && isset($labData[0]['resource']->organization->name)) 
                        ? $labData[0]['resource']->organization->name 
                        : 'Semua Lembaga' 
                }}</strong></td>
            </tr>
            <tr>
                <td style="width: 120px;">Unit Kerja</td>
                <td>: <strong contenteditable="true">{{ $labName }}</strong></td>
            </tr>
            <tr>
                <td>Periode</td>
                <td>: <span contenteditable="true">{{ $monthName }} {{ $year }}</span></td>
            </tr>
        </table>

        {{-- SUMMARY --}}
        <div class="section-title">Ringkasan Penggunaan</div>
        <div class="summary-grid">
            <div class="summary-card">
                <div class="summary-val" contenteditable="true">{{ number_format($summary['total_capacity']) }}</div>
                <div class="summary-key">Total Kapasitas</div>
            </div>
            <div class="summary-card">
                <div class="summary-val" contenteditable="true">{{ number_format($summary['total_scheduled']) }}</div>
                <div class="summary-key">Jadwal Tetap</div>
            </div>
            <div class="summary-card">
                <div class="summary-val" contenteditable="true">{{ number_format($summary['total_booking']) }}</div>
                <div class="summary-key">Booking</div>
            </div>
            <div class="summary-card">
                <div class="summary-val" contenteditable="true">{{ $summary['total_pct'] }}%</div>
                <div class="summary-key">Tingkat Penggunaan</div>
            </div>
        </div>

        {{-- RINGKASAN PENGGUNAAN LEMBAGA --}}
        @if(isset($lembagaUsage) && count($lembagaUsage) > 0)
        <div class="section-title">🏫 Ringkasan Penggunaan Lembaga</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th class="tc">No</th>
                    <th>Lembaga</th>
                    <th class="tc">Total Kapasitas</th>
                    <th class="tc">Digunakan</th>
                    <th class="tc">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php $idx = 1; @endphp
                @foreach($lembagaUsage as $lembagaName => $data)
                <tr>
                    <td class="tc" contenteditable="true">{{ $idx++ }}</td>
                    <td contenteditable="true">{{ $lembagaName }}</td>
                    <td class="tc" contenteditable="true">{{ number_format($data['totalCapacity']) }}</td>
                    <td class="tc" contenteditable="true">{{ number_format($data['totalUsed']) }}</td>
                    <td class="tc" contenteditable="true">
                        {{ $data['totalCapacity'] > 0 ? number_format(($data['totalUsed'] / $data['totalCapacity']) * 100, 2) : 0 }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- DATA PER LAB --}}
        @foreach($labData as $lab)
        <div class="section-title">📊 {{ $lab['resource']->name }} @if($lab['resource']->organization) ({{ $lab['resource']->organization->name }}) @endif</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th class="tc">No</th>
                    <th>Jenis</th>
                    <th class="tc">Total</th>
                    <th class="tc">Digunakan</th>
                    <th class="tc">Kosong</th>
                    <th class="tc">Persentase</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="tc" contenteditable="true">1</td>
                    <td contenteditable="true">Jadwal Tetap</td>
                    <td class="tc" contenteditable="true">{{ number_format($lab['totalCapacity']) }}</td>
                    <td class="tc" contenteditable="true">{{ number_format($lab['scheduledSlots']) }}</td>
                    <td class="tc" contenteditable="true">{{ number_format($lab['totalCapacity'] - $lab['scheduledSlots']) }}</td>
                    <td class="tc" contenteditable="true">{{ number_format(($lab['scheduledSlots'] / $lab['totalCapacity']) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td class="tc" contenteditable="true">2</td>
                    <td contenteditable="true">Booking</td>
                    <td class="tc" contenteditable="true">{{ number_format($lab['totalCapacity']) }}</td>
                    <td class="tc" contenteditable="true">{{ number_format($lab['bookingSlots']) }}</td>
                    <td class="tc" contenteditable="true">{{ number_format($lab['totalCapacity'] - $lab['bookingSlots']) }}</td>
                    <td class="tc" contenteditable="true">{{ number_format(($lab['bookingSlots'] / $lab['totalCapacity']) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td class="tc" contenteditable="true">3</td>
                    <td contenteditable="true">Total</td>
                    <td class="tc" contenteditable="true">{{ number_format($lab['totalCapacity']) }}</td>
                    <td class="tc" contenteditable="true">{{ number_format($lab['totalUsed']) }}</td>
                    <td class="tc" contenteditable="true">{{ number_format($lab['totalFree']) }}</td>
                    <td class="tc" contenteditable="true">{{ number_format($lab['percentage'], 1) }}%</td>
                </tr>
            </tbody>
        </table>

        {{-- TOP TEACHERS --}}
        @if(count($lab['teacherUsage']) > 0)
        <div class="section-title">🏆 Top Pengajar Bulan Ini</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th class="tc">No</th>
                    <th>Nama Guru</th>
                    <th>Lembaga</th>
                    <th class="tc">Total Sesi</th>
                    <th class="tc">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php $idx = 1; @endphp
                @foreach($lab['teacherUsage'] as $name => $count)
                <tr>
                    <td class="tc" contenteditable="true">{{ $idx++ }}</td>
                    <td contenteditable="true">{{ $name }}</td>
                    <td contenteditable="true">{{ $lab['resource']->organization->name ?? '-' }}</td>
                    <td class="tc" contenteditable="true">{{ $count }}</td>
                    <td class="tc" contenteditable="true">
                        {{ $lab['totalUsed'] > 0 ? number_format(($count / $lab['totalUsed']) * 100, 2) : 0 }}%
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- SCHEDULE DETAILS --}}
        @if($lab['scheduleDetails']->count() > 0)
        <div class="section-title">📅 Detail Jadwal Tetap</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th class="tc">No</th>
                    <th>Hari</th>
                    <th>Slot Waktu</th>
                    <th>Kelas</th>
                    <th>Guru / Pengajar</th>
                    <th class="tc">Frekuensi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lab['scheduleDetails'] as $idx => $sch)
                <tr>
                    <td class="tc" contenteditable="true">{{ $idx + 1 }}</td>
                    <td contenteditable="true">{{ $sch->day_name ?? $sch->day_of_week }}</td>
                    <td contenteditable="true">{{ $sch->timeSlot?->name ?? '-' }}</td>
                    <td contenteditable="true">{{ $sch->labClass?->name ?? '-' }}</td>
                    <td contenteditable="true">{{ $sch->teacher_name ?? '-' }}</td>
                    <td class="tc" contenteditable="true">{{ $sch->occurrences }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- BOOKING DETAILS --}}
        @if($lab['bookingDetails']->count() > 0)
        <div class="section-title">📝 Detail Booking</div>
        <table class="main-table">
            <thead>
                <tr>
                    <th class="tc">No</th>
                    <th>Tanggal</th>
                    <th>Slot Waktu</th>
                    <th>Guru / Pengajar</th>
                    <th>Kelas</th>
                    <th>Keperluan</th>
                    <th class="tc">Peserta</th>
                </tr>
            </thead>
            <tbody>
                @foreach($lab['bookingDetails'] as $idx => $book)
                <tr>
                    <td class="tc" contenteditable="true">{{ $idx + 1 }}</td>
                    <td contenteditable="true">{{ \Carbon\Carbon::parse($book->booking_date)->translatedFormat('d M Y') }}</td>
                    <td contenteditable="true">{{ $book->timeSlot?->name ?? '-' }}</td>
                    <td contenteditable="true">{{ $book->teacher_name ?? '-' }}</td>
                    <td contenteditable="true">{{ $book->class_name ?? '-' }}</td>
                    <td contenteditable="true">{{ $book->title ?? '-' }}</td>
                    <td class="tc" contenteditable="true">{{ $book->participant_count ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
        @endforeach

        {{-- TANDA TANGAN --}}
        <div class="footer">
            <div class="ttd-box">
                <div contenteditable="true">Jember, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
                <div contenteditable="true" style="margin-top: 5px;">Mengetahui,</div>
                <div class="ttd-space"></div>
                <div class="ttd-name" contenteditable="true">{{ auth()->user()->full_name ?? '(Nama Pengelola)' }}</div>
                <div contenteditable="true">NIP/NIY. ...........................</div>
            </div>
        </div>
    </div>

</body>
</html>
