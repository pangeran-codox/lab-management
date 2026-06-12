<!DOCTYPE html>
<html>
<head>
    <title>Laporan Inventaris Laboratorium</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #666; }
        .info { margin-bottom: 20px; }
        .info table { width: 100%; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background-color: #f2f2f2; font-weight: bold; text-align: left; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        .tc { text-align: center; }
        .tr { text-align: right; }
        .footer { margin-top: 50px; text-align: right; }
        .footer .signature { margin-top: 60px; font-weight: bold; text-decoration: underline; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .cond-excellent { background-color: #dcfce7; color: #15803d; }
        .cond-good { background-color: #dcfce7; color: #15803d; }
        .cond-broken { background-color: #fee2e2; color: #b91c1c; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Inventaris Laboratorium</h1>
        <p>Nuris Jember · Sistem Manajemen Laboratorium</p>
    </div>

    <div class="info">
        <table>
            <tr>
                <td style="border:none; padding:0; width: 120px;">Laboratorium</td>
                <td style="border:none; padding:0;">: <strong>{{ $labName }}</strong></td>
            </tr>
            <tr>
                <td style="border:none; padding:0;">Tanggal Cetak</td>
                <td style="border:none; padding:0;">: {{ $date }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 30px;" class="tc">No</th>
                <th>Nama Barang</th>
                <th>Lab</th>
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
                    <strong>{{ $item->item_name }}</strong><br>
                    <small style="color: #666;">{{ $item->brand }} {{ $item->model }}</small>
                </td>
                <td>{{ $item->resource->name ?? '-' }}</td>
                <td>{{ ucfirst($item->category) }}</td>
                <td class="tc">{{ $item->quantity }}</td>
                <td class="tc">{{ $item->quantity_good }}</td>
                <td class="tc" style="{{ $item->quantity_broken > 0 ? 'color: red; font-weight: bold;' : '' }}">
                    {{ $item->quantity_broken }}
                </td>
                <td class="tc">
                    <span class="badge cond-{{ $item->condition }}">
                        {{ $item->condition }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Jember, {{ $date }}</p>
        <p>Mengetahui,</p>
        <div class="signature">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        </div>
        <p>Kepala Lab / Teknisi</p>
    </div>
</body>
</html>
