<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Laporan Inventaris</title>
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
            
            .toolbar { 
                flex-wrap: wrap; 
                gap: 12px; 
                padding: 12px 16px;
                border-radius: 10px;
            }
            .toolbar-title { 
                font-size: 13px; 
                flex-basis: 100%; 
                text-align: center; 
                order: 3;
                justify-content: center;
            }
            .btn-back, .btn-print { 
                font-size: 13px; 
                padding: 10px 14px; 
                flex: 1;
                justify-content: center;
            }
            
            .page {
                width: 100%; 
                min-height: auto; 
                padding: 18px; 
                margin: 0 auto;
                box-shadow: 0 4px 20px rgba(0, 61, 36, 0.08);
                border-radius: 12px;
            }

            .kop { flex-direction: column; text-align: center; gap: 16px; }
            .kop-logo { margin-right: 0; }
            .kop-h1 { font-size: 18px; }
            .kop-h2 { font-size: 15px; }
            .kop-p { font-size: 11px; }

            .main-table, .info-table {
                font-size: 10px; 
                display: block; 
                overflow-x: auto;
            }

            .footer { justify-content: center; }
        }

        /* ── Kop Surat ──────────────────────────────── */
        .kop { 
            display: flex; 
            align-items: center; 
            border-bottom: 3px solid var(--p); 
            padding-bottom: 18px; 
            margin-bottom: 32px; 
        }
        .kop-logo { 
            width: 90px; 
            height: 90px; 
            margin-right: 24px; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            background: linear-gradient(135deg, var(--p) 0%, var(--p-light) 100%);
            border-radius: 20px;
            color: white;
            box-shadow: 0 4px 14px rgba(0, 61, 36, 0.2);
        }
        .kop-text { flex: 1; text-align: center; }
        .kop-h1 { 
            font-size: 24px; 
            font-weight: 800; 
            margin: 0; 
            text-transform: uppercase; 
            font-family: 'Outfit', sans-serif; 
            color: var(--p);
        }
        .kop-h2 { 
            font-size: 18px; 
            font-weight: 700; 
            margin: 8px 0 4px; 
            color: var(--p-light); 
        }
        .kop-p { 
            font-size: 12px; 
            margin: 0; 
            color: #64748b; 
            font-style: italic; 
        }

        /* ── Content ────────────────────────────────── */
        .report-title { 
            text-align: center; 
            font-size: 20px; 
            font-weight: 800; 
            margin-bottom: 30px; 
            text-transform: uppercase; 
            color: var(--p);
            position: relative;
            display: inline-block;
            width: 100%;
        }
        .report-title::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(90deg, var(--p) 0%, var(--s) 100%);
            border-radius: 2px;
        }
        
        .info-table { 
            width: 100%; 
            margin-bottom: 28px; 
            font-size: 13px; 
            background: var(--p-lighter);
            padding: 16px 20px;
            border-radius: 12px;
            border-left: 4px solid var(--p);
        }
        .info-table td { padding: 4px 0; }
        .info-table td:first-child { 
            font-weight: 700; 
            color: var(--p);
            min-width: 110px;
        }
        
        .main-table { 
            width: 100%; 
            border-collapse: collapse; 
            font-size: 12px; 
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .main-table thead {
            background: linear-gradient(135deg, var(--p) 0%, var(--p-light) 100%);
        }
        .main-table th { 
            color: white;
            border: none; 
            padding: 12px 10px; 
            font-weight: 700; 
            text-align: left; 
            font-family: 'Outfit', sans-serif;
            font-size: 12px;
        }
        .main-table tbody tr:nth-child(even) {
            background: #f8faf7;
        }
        .main-table tbody tr:hover {
            background: #e6f4ee;
        }
        .main-table td { 
            border: 1px solid #e2e8f0; 
            padding: 10px 8px; 
            vertical-align: top; 
        }
        .tc { text-align: center; }

        /* ── Tanda Tangan ───────────────────────────── */
        .footer { margin-top: 50px; display: flex; justify-content: flex-end; }
        .ttd-box { 
            width: 250px; 
            text-align: center; 
            background: #f8faf7;
            padding: 20px 16px;
            border-radius: 12px;
            border: 1px dashed var(--p);
        }
        .ttd-space { height: 90px; }
        .ttd-name { 
            font-weight: 800; 
            text-decoration: underline; 
            margin-bottom: 5px; 
            color: var(--p);
        }

        /* ── Editable Styles ────────────────────────── */
        [contenteditable="true"]:hover { 
            background: #fffde7; 
            outline: 2px dashed #f59e0b; 
            cursor: text; 
            border-radius: 4px;
        }
        [contenteditable="true"]:focus { 
            background: #fffbeb; 
            outline: 2px solid var(--p); 
            border-radius: 4px;
        }

        /* ── Print Styles ───────────────────────────── */
        @media print {
            body { 
                background: white; 
                padding: 0;
            }
            .toolbar { display: none; }
            .page { 
                margin: 0; 
                box-shadow: none; 
                width: 100%; 
                padding: 0; 
                border-radius: 0;
            }
            [contenteditable="true"]:hover, [contenteditable="true"]:focus { 
                outline: none; 
                background: none; 
            }
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
        <div class="report-title">Laporan Inventaris Barang Laboratorium</div>

        {{-- INFO --}}
        <table class="info-table">
            <tr>
                <td style="width: 120px;">Unit Kerja</td>
                <td>: <strong>{{ $labName }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal Laporan</td>
                <td>: <span>{{ $date }}</span></td>
            </tr>
            <tr>
                <td>Kategori</td>
                <td>: <span>Semua Aset</span></td>
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
                        <div style="font-weight: 800;">{{ $item->item_name }}</div>
                        @if($item->brand || $item->model)
                        <div style="font-size: 10px; color: #555;">{{ $item->brand }} {{ $item->model }}</div>
                        @endif
                        @if($item->specifications)
                        <div style="font-size: 10px; color: #555;">{{ $item->specifications }}</div>
                        @endif
                    </td>
                    <td>{{ ucfirst($item->category) }}</td>
                    <td class="tc">{{ $item->quantity }}</td>
                    <td class="tc">{{ $item->quantity_good }}</td>
                    <td class="tc" style="{{ $item->quantity_broken > 0 ? 'color:red; font-weight:bold;' : '' }}">
                        {{ $item->quantity_broken }}
                    </td>
                    <td class="tc">{{ ucfirst($item->condition) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- TANDA TANGAN --}}
        <div class="footer">
            <div class="ttd-box">
                <div>Jember, {{ $date }}</div>
                <div style="margin-top: 5px;">Mengetahui,</div>
                <div class="ttd-space"></div>
                <div class="ttd-name" contenteditable="true">{{ auth()->user()->full_name ?? '(Nama Pengelola)' }}</div>
                <div contenteditable="true">NIP/NIY. ...........................</div>
            </div>
        </div>
    </div>

</body>
</html>
