<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Laporan Inventaris</title>
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

        .main-table { width: 100%; border-collapse: collapse; font-size: 12px; }
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
        <a href="javascript:history.back()" class="btn-back" style="text-decoration: none; color: white; background: #64748b; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; margin-right: 15px;">
            ← Kembali
        </a>
        <div class="toolbar-title" style="flex: 1;">📝 Editor Laporan Inventaris (Klik teks untuk mengedit sebelum cetak)</div>
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
        <div class="report-title" contenteditable="true">Laporan Inventaris Barang Laboratorium</div>

        {{-- INFO --}}
        <table class="info-table">
            <tr>
                <td style="width: 120px;">Unit Kerja</td>
                <td>: <strong contenteditable="true">{{ $labName }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Laporan</td>
                <td>: <span contenteditable="true">{{ $date }}</span></td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>: <span contenteditable="true">Semua Aset</span></td>
            </tr>
        </table>

        {{-- TABLE --}}
        <table class="main-table">
            <thead>
                <tr>
                    <th class="tc" style="width: 30px;">No</th>
                    <th>Nama Barang / Spesifikasi</th>
                    <th>Kategori</th>
                    <th class="tc">Total</th>
                    <th class="tc">Baik</th>
                    <th class="tc">Rusak</th>
                    <th class="tc">Kondisi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                <tr>
                    <td class="tc">{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: 800;" contenteditable="true">{{ $item->item_name }}</div>
                        <div style="font-size: 10px; color: #555;" contenteditable="true">{{ $item->brand }} {{ $item->model }}</div>
                    </td>
                    <td contenteditable="true">{{ ucfirst($item->category) }}</td>
                    <td class="tc" contenteditable="true">{{ $item->quantity }}</td>
                    <td class="tc" contenteditable="true">{{ $item->quantity_good }}</td>
                    <td class="tc" contenteditable="true" style="{{ $item->quantity_broken > 0 ? 'color:red; font-weight:bold;' : '' }}">
                        {{ $item->quantity_broken }}
                    </td>
                    <td class="tc" contenteditable="true">{{ ucfirst($item->condition) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- TANDA TANGAN --}}
        <div class="footer">
            <div class="ttd-box">
                <div contenteditable="true">Jember, {{ $date }}</div>
                <div contenteditable="true" style="margin-top: 5px;">Mengetahui,</div>
                <div class="ttd-space"></div>
                <div class="ttd-name" contenteditable="true">{{ auth()->user()->full_name ?? '(Nama Pengelola)' }}</div>
                <div contenteditable="true">NIP/NIY. ...........................</div>
            </div>
        </div>
    </div>

</body>
</html>
