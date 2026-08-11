# x-report-editor — Component Editor Laporan Universal

Satu sumber kebenaran untuk toolbar, kop surat, kontrol ukuran font,
dropdown ukuran kertas, dan tanda tangan — dipakai oleh semua halaman
editor laporan (inventaris, rekap, dan laporan baru ke depan) supaya
tidak ada lagi banyak file HTML kembar yang gampang beda-beda.

File ini `.md` biasa, **bukan** `.blade.php` — jadi contoh kode di bawah
tidak akan pernah ikut dikompilasi Blade. Aman ditaruh di folder yang sama
dengan component-nya.

## Cara pakai

```blade
<x-report-editor
    page-title="Editor Laporan Inventaris"
    report-title="Laporan Inventaris Barang Laboratorium"
    :back-url="url()->previous()"
    header-hint="Klik teks untuk mengedit sebelum cetak"
    :logo="$logo"
    :site-name="$siteName"
    :site-address="$siteAddress"
    :site-phone="$sitePhone"
    :kop-name-size="$kopNameSize"
    :kop-address-size="$kopAddressSize"
    :kop-phone-size="$kopPhoneSize"
    :site-head-name="$siteHeadName"
    :report-footer="$reportFooter"
>
    <x-slot:info>
        <tr><td style="width:120px">Unit Kerja</td><td>: <strong>{{ $labName }}</strong></td></tr>
        <tr><td>Tanggal Laporan</td><td>: {{ $date }}</td></tr>
    </x-slot:info>

    <x-slot:summary>
        {{-- opsional — isi div.summary-grid di sini jika laporan butuh kartu ringkasan --}}
    </x-slot:summary>

    <table class="main-table">
        {{-- konten tabel utama laporan --}}
    </table>
</x-report-editor>
```

## Daftar props

| Prop | Default | Keterangan |
|---|---|---|
| `page-title` | `'Editor Laporan'` | Judul tab browser |
| `report-title` | `'Laporan'` | Judul besar di halaman |
| `back-url` | `null` | URL tombol kembali (kalau `null`, tombol tidak ditampilkan) |
| `header-hint` | `null` | Teks kecil di sebelah judul toolbar |
| `logo` | `null` | Path logo (relatif ke `storage/`), fallback ikon default |
| `site-name` / `site-address` / `site-phone` | `''` | Isi kop surat |
| `kop-name-size` / `kop-address-size` / `kop-phone-size` | `20` / `13` / `12` | Ukuran font kop (px) |
| `site-head-name` | `''` | Nama penandatangan; fallback otomatis ke nama user login, lalu `'(Nama Pengelola)'` |
| `report-footer` | `''` | Teks footer di bawah tanda tangan (opsional) |
| `signature-city` | `'Jember'` | Kota pada baris tanda tangan |
| `signature-date` | `null` | Tanggal tanda tangan; default hari ini |
| `paper-size` / `paper-size-label` | `'A4 portrait'` / `'A4 Portrait'` | Ukuran kertas default saat halaman dibuka |

## Slot

| Slot | Wajib? | Keterangan |
|---|---|---|
| default (isi langsung di antara tag) | Ya | Konten tabel utama laporan |
| `info` | Tidak | Baris-baris info laporan (dirender di dalam `<table class="info-table">`) |
| `summary` | Tidak | Kartu ringkasan angka (render bebas, biasanya `div.summary-grid`) |
| `signature` | Tidak | Override total blok tanda tangan; kalau tidak diisi, dipakai blok tanda tangan default |

## Catatan penting

- Semua variabel meta (`logo`, `siteName`, `kopNameSize`, dst) sebaiknya
  disuplai dari satu trait controller (mis. `BuildsReportMeta`) supaya
  tiap controller tidak perlu memanggil `Setting::get()` berulang.
- Fallback nama penandatangan sudah aman untuk guest (memakai
  `optional(auth()->user())`) — tidak perlu ditulis ulang di controller/view pemanggil.
- Endpoint simpan ukuran kop tetap `settings.kop-size` (sama seperti
  sebelumnya). Kalau nanti mau menyimpan ukuran kertas juga, tinggal
  tambahkan field ke payload yang sama — jangan bikin endpoint baru.

## ⚠️ Peringatan untuk siapa pun yang mengedit file component ini

**Jangan taruh contoh kode Blade (`<x-...>`, `{{ }}`, dsb.) di dalam
komentar apa pun langsung di file `.blade.php`** — baik komentar Blade
(`{{-- --}}`) maupun komentar HTML (`<!-- -->`). Keduanya tetap
membiarkan Blade mengompilasi tag/echo di dalamnya kalau bentuknya
tidak nested dengan benar, atau (untuk `<!-- -->`) selalu dikompilasi
apa pun isinya. Taruh contoh kode di file dokumentasi terpisah seperti
ini (`.md`), bukan di dalam file `.blade.php` itu sendiri.
