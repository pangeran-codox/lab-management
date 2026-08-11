# Product Requirements Document (PRD)
## Lab Management System — SMKS Nuris Jember

**Versi:** 2.1  
**Terakhir diperbarui:** Juli 2026  
**Status:** Active Development

---

## Daftar Isi

- [Latar Belakang](#latar-belakang)
- [Tujuan Produk](#tujuan-produk)
- [Pengguna & Personas](#pengguna--personas)
- [Fitur & Requirement](#fitur--requirement)
- [User Stories](#user-stories)
- [Non-Functional Requirements](#non-functional-requirements)
- [Batasan Sistem](#batasan-sistem)
- [Roadmap](#roadmap)

---

## Latar Belakang

SMKS Nuris Jember memiliki beberapa laboratorium komputer yang digunakan untuk kegiatan belajar mengajar setiap hari. Sebelum sistem ini ada, pengelolaan lab dilakukan secara manual:

- **Jadwal** ditulis di papan tulis atau spreadsheet Excel, tidak bisa diakses real-time
- **Booking lab** dilakukan via pesan WhatsApp ke petugas lab, rawan konflik dan sering terlewat
- **Jurnal penggunaan** dicatat di buku fisik, sulit direkap dan bisa hilang
- **Tugas siswa** dikumpulkan via flashdisk atau grup WA kelas, tidak terstruktur
- **Inventaris** dicatat di spreadsheet terpisah, tidak ada alert kerusakan
- **Keuangan lab** dikelola manual di buku kas, tidak ada laporan yang konsisten
- **Internet lab** dimatikan/dihidupkan manual oleh teknisi di ruang server

Semua masalah ini menyebabkan inefisiensi, konflik penggunaan lab, dan sulitnya rekap data untuk keperluan administrasi.

---

## Tujuan Produk

1. **Transparansi** — Semua orang (guru, siswa, staff) bisa melihat jadwal dan status lab secara real-time
2. **Efisiensi** — Booking dan approval bisa dilakukan tanpa tatap muka
3. **Akuntabilitas** — Setiap penggunaan lab terdokumentasi (jurnal + foto)
4. **Sentralisasi** — Satu platform untuk semua kebutuhan pengelolaan lab
5. **Otomasi** — Notifikasi WA otomatis, kontrol internet otomatis sesuai jadwal

---

## Pengguna & Personas

### 1. Guru / Pengajar
- **Kebutuhan:** Lihat jadwal lab, booking lab untuk kegiatan insidental, buat tugas untuk siswa, lihat siapa yang sudah kumpul tugas
- **Akses:** Halaman publik (tanpa login) + portal tugas-admin via token
- **Pain point:** Tidak tahu apakah lab kosong, proses booking manual tidak praktis

### 2. Siswa
- **Kebutuhan:** Lihat jadwal, kumpulkan tugas, unduh soal dari guru
- **Akses:** Halaman publik (tanpa login), halaman tugas via PIN kelas
- **Pain point:** Tidak tahu batas waktu pengumpulan tugas, flashdisk hilang

### 3. Petugas / Operator Lab
- **Kebutuhan:** Kelola jadwal, approve/reject booking, pantau status lab, kelola inventaris
- **Akses:** Panel admin (login required)
- **Pain point:** Banyak permintaan booking via WA yang tidak terstruktur

### 4. Teknisi Lab
- **Kebutuhan:** Pantau dan kontrol internet per lab, catat kerusakan inventaris
- **Akses:** Panel admin (login required, terbatas pada lab yang ditugaskan)
- **Pain point:** Harus ke ruang server untuk matikan/hidupkan internet

### 5. Kepala Lab / Admin
- **Kebutuhan:** Rekap penggunaan lab, laporan inventaris, kelola pengguna sistem, pantau semua aktivitas
- **Akses:** Panel admin penuh
- **Pain point:** Sulit membuat laporan dari data yang tersebar

### 6. Bendahara / Staff Keuangan
- **Kebutuhan:** Catat pemasukan/pengeluaran lab, buat laporan keuangan, terima notifikasi transaksi
- **Akses:** Modul finance (guard terpisah)
- **Pain point:** Tidak ada sistem pencatatan keuangan yang terintegrasi

---

## Fitur & Requirement

### FR-01: Jadwal Lab (Publik)

**Prioritas:** Wajib  
**Deskripsi:** Tampilan jadwal mingguan semua lab yang bisa diakses tanpa login.

| ID | Requirement |
|----|-------------|
| FR-01.1 | Tampilkan jadwal mingguan dalam format grid (hari × slot waktu) per lab |
| FR-01.2 | Navigasi minggu (prev/next) tanpa reload halaman via AJAX |
| FR-01.3 | Highlight hari ini, tandai slot yang sudah lewat |
| FR-01.4 | Tampilkan status slot: jadwal tetap, booking disetujui, booking pending, tersedia |
| FR-01.5 | Update otomatis via WebSocket saat ada booking baru atau perubahan status |
| FR-01.6 | Tampilkan jadwal penting yang memblokir slot tertentu |
| FR-01.7 | Guru bisa klik slot kosong untuk form booking langsung dari halaman ini |

### FR-02: Booking Lab

**Prioritas:** Wajib  
**Deskripsi:** Guru bisa request booking lab untuk tanggal dan slot waktu tertentu.

| ID | Requirement |
|----|-------------|
| FR-02.1 | Form booking minimal: nama guru, judul kegiatan, kelas, mata pelajaran, tanggal, slot waktu |
| FR-02.2 | Cek konflik otomatis — tidak boleh ada dua booking aktif di slot yang sama |
| FR-02.3 | Status booking: pending → approved / rejected |
| FR-02.4 | Admin bisa approve/reject dari tabel mingguan maupun daftar booking |
| FR-02.5 | Approve group: setujui semua slot dalam satu sesi sekaligus |
| FR-02.6 | Notifikasi WA ke admin saat ada booking baru (opsional, konfigurasi) |
| FR-02.7 | Booking khusus hari Minggu (full day) dengan alur approval terpisah |
| FR-02.8 | Tabel mingguan booking di panel admin update realtime via WebSocket |

### FR-03: Jurnal Lab

**Prioritas:** Wajib  
**Deskripsi:** Pencatatan penggunaan lab harian dengan foto sebagai bukti.

| ID | Requirement |
|----|-------------|
| FR-03.1 | Halaman jurnal dapat diakses tanpa login (publik) |
| FR-03.2 | Tampilkan jadwal hari ini per lab, kelompokkan slot yang berurutan |
| FR-03.3 | Guru bisa isi jurnal untuk sesinya dengan catatan dan minimal 1 foto |
| FR-03.4 | Upload maksimal 5 foto per sesi, masing-masing max 5MB |
| FR-03.5 | Foto ditampilkan langsung di tabel jadwal (140×105px) untuk preview |
| FR-03.6 | Navigasi tanggal (prev/next/pilih tanggal) |
| FR-03.7 | Submit jurnal hanya diizinkan dalam window waktu yang valid (eligibility check) |

### FR-04: Tugas Siswa

**Prioritas:** Wajib  
**Deskripsi:** Sistem pengumpulan tugas lengkap tanpa siswa perlu membuat akun.

| ID | Requirement |
|----|-------------|
| FR-04.1 | Siswa akses halaman tugas dengan PIN 6 digit yang diberikan guru |
| FR-04.2 | Tampilkan daftar tugas aktif dengan card yang jelas: strip warna status, deadline besar, countdown realtime per detik |
| FR-04.3 | Siswa upload file tugas (pdf/doc/docx/ppt/pptx/xls/xlsx/zip/rar, max 5MB) |
| FR-04.4 | Countdown deadline realtime per detik di card tugas |
| FR-04.5 | Setelah deadline, form pengumpulan ditutup otomatis |
| FR-04.6 | Guru akses portal admin tugas via token (tanpa login akun sistem) |
| FR-04.7 | Guru bisa buat tugas, lihat siapa yang sudah kumpul, download file, beri nilai |
| FR-04.8 | Guru bisa upload soal/attachment yang bisa diunduh siswa (guard: PIN kelas, tanpa token) |
| FR-04.9 | Tugas baru muncul di halaman siswa realtime saat guru buka akses (tanpa reload) |
| FR-04.10 | Tugas hilang dari halaman siswa realtime saat guru tutup akses (tanpa reload) |
| FR-04.11 | Sistem tolak submit ulang jika nama sudah ada (kecuali guru aktifkan allow_resubmit) |
| FR-04.12 | Guru bisa download semua submission satu kelas sebagai file ZIP |
| FR-04.13 | Guru bisa export daftar nama + nilai ke Excel (.xls dengan warna nilai otomatis) |
| FR-04.14 | Guru bisa hapus submission individual (file + record dihapus permanen) |
| FR-04.15 | Guru bisa edit tugas: judul, mapel, deadline, keterangan, ganti soal |
| FR-04.16 | Guru bisa buat tugas lanjutan (series) dalam satu rangkaian pertemuan |
| FR-04.17 | Guru bisa perpanjang deadline / buka ulang pengumpulan dengan round baru |
| FR-04.18 | Guru bisa aktifkan/nonaktifkan akses download submission untuk siswa (toggle) |
| FR-04.19 | Saat download diaktifkan: tombol Download muncul realtime di tiap baris submission halaman siswa |
| FR-04.20 | Semua aksi admin terlindungi — guru hanya bisa akses tugasnya sendiri (isolasi per token) |

### FR-05: Inventaris Lab

**Prioritas:** Wajib  
**Deskripsi:** Manajemen aset dan kondisi perangkat di setiap lab.

| ID | Requirement |
|----|-------------|
| FR-05.1 | CRUD inventaris: nama, kategori, jumlah, kondisi, lokasi (lab) |
| FR-05.2 | Update kondisi cepat (quick update) tanpa buka form penuh |
| FR-05.3 | Log perbaikan/maintenance per item inventaris |
| FR-05.4 | Halaman publik tampilkan daftar inventaris per lab |
| FR-05.5 | Export laporan inventaris ke PDF |
| FR-05.6 | Filter inventaris berdasarkan lab, kategori, kondisi |
| FR-05.7 | Halaman terpusat untuk semua barang rusak lintas lab |
| FR-05.8 | Form perbaikan inline per item: catat jumlah diperbaiki, jenis, biaya, deskripsi |
| FR-05.9 | Perbaikan otomatis memindahkan unit rusak → baik dan update kondisi berdasarkan rasio |
| FR-05.10 | Badge counter barang rusak di sidebar dan tombol navigasi inventaris |

### FR-06: Kontrol Internet Lab

**Prioritas:** Tinggi  
**Deskripsi:** Teknisi bisa nyalakan/matikan internet per lab dari mana saja.

| ID | Requirement |
|----|-------------|
| FR-06.1 | Setiap lab punya link kontrol dengan token unik |
| FR-06.2 | Toggle internet (on/off) tanpa login akun sistem |
| FR-06.3 | Tampilkan status realtime: online/offline + jumlah perangkat aktif |
| FR-06.4 | Admin bisa generate ulang token lab |
| FR-06.5 | Konfigurasi device MikroTik dan mapping lab dari panel admin |

### FR-07: Modul Finance

**Prioritas:** Tinggi  
**Deskripsi:** Pencatatan keuangan lab yang terpisah dari sistem lab utama.

| ID | Requirement |
|----|-------------|
| FR-07.1 | Autentikasi terpisah dari sistem lab (guard berbeda) |
| FR-07.2 | CRUD transaksi: pemasukan dan pengeluaran dengan kategori |
| FR-07.3 | Manajemen anggaran (budget) per kategori per periode |
| FR-07.4 | Dashboard ringkasan keuangan (total in/out, saldo, grafik) |
| FR-07.5 | Laporan keuangan dengan filter periode |
| FR-07.6 | Notifikasi WA otomatis ke nomor/grup yang dikonfigurasi saat transaksi baru |
| FR-07.7 | Alert WA saat penggunaan anggaran mencapai threshold tertentu (default 80%) |

### FR-08: File Manager

**Prioritas:** Sedang  
**Deskripsi:** Dashboard untuk melihat dan mengelola semua file yang diupload.

| ID | Requirement |
|----|-------------|
| FR-08.1 | Tampilkan semua foto jurnal dan file tugas dalam satu halaman |
| FR-08.2 | Filter berdasarkan tipe (jurnal/tugas) |
| FR-08.3 | Tampilkan ukuran file dan total kapasitas yang dipakai |
| FR-08.4 | Deteksi file yang ada di database tapi tidak ada di storage (orphaned) |
| FR-08.5 | Admin bisa download dan hapus file |
| FR-08.6 | Pencarian berdasarkan nama guru/siswa, kelas, nama file |

---

## User Stories

### Guru

```
Sebagai guru, saya ingin:
- Melihat jadwal lab minggu ini tanpa harus login
  agar saya bisa merencanakan kegiatan laboratorium

- Booking lab untuk kegiatan insidental dengan mudah
  agar tidak perlu datang ke ruang petugas

- Mendapat konfirmasi approval booking via WA
  agar saya tahu apakah permintaan saya disetujui

- Mengisi jurnal penggunaan lab hari ini dengan foto
  agar ada bukti dokumentasi penggunaan lab

- Membuat tugas untuk siswa dan melihat siapa yang sudah kumpul
  agar pengelolaan tugas lebih terstruktur
```

### Siswa

```
Sebagai siswa, saya ingin:
- Mengumpulkan tugas tanpa perlu akun
  agar prosesnya lebih mudah dan cepat

- Melihat countdown deadline tugas
  agar saya tidak melewatkan batas waktu

- Mengunduh soal yang disediakan guru
  agar saya bisa mengerjakan tugas dengan benar
```

### Operator Lab

```
Sebagai operator lab, saya ingin:
- Melihat semua booking yang pending dalam satu tampilan
  agar bisa segera diproses

- Approve atau reject booking dengan satu klik
  agar proses approval lebih efisien

- Mendapat notifikasi WA saat ada booking baru
  agar saya tidak melewatkan permintaan
```

### Teknisi

```
Sebagai teknisi, saya ingin:
- Menyalakan atau mematikan internet lab dari HP
  agar tidak perlu ke ruang server setiap kali

- Melihat jumlah perangkat yang aktif di lab
  agar saya bisa memastikan lab kosong sebelum mematikan internet

- Mencatat kerusakan inventaris dengan foto
  agar ada rekam jejak perbaikan
```

---

## Non-Functional Requirements

### Performance

| Metric | Target |
|--------|--------|
| Page load time | < 2 detik untuk halaman publik |
| Realtime update (WebSocket) | < 500ms setelah event |
| AJAX partial reload (booking weekly grid) | < 1 detik |
| PDF generation | < 5 detik untuk laporan 1 bulan |
| File upload (5MB) | < 10 detik |

### Availability

- Uptime target: **99.5%** (dev/staging tidak dihitung)
- Maintenance window: Jumat malam 21:00–23:00
- Backup database: harian otomatis

### Security

- Semua endpoint publik yang mengubah data menggunakan CSRF token
- Rate limiting di semua endpoint publik yang menerima input user
- File upload: validasi tipe MIME di server, bukan hanya ekstensi
- File tugas tidak bisa diakses langsung via URL publik
- Password hash menggunakan bcrypt (via Laravel)
- Environment secrets tidak di-commit ke repository

### Scalability

- Horizontal scaling di layer app (Docker Swarm replicas)
- Stateless session via Redis (mendukung multiple replicas)
- Queue worker terpisah (Horizon) untuk proses background
- Cache strategis untuk query berat (dashboard, weekly booking grid)

### Browser Support

- Chrome 90+, Firefox 90+, Safari 14+, Edge 90+
- Mobile-responsive untuk halaman publik
- DataTransfer API (drag-and-drop file) — graceful degradation di browser lama

---

## Batasan Sistem

1. **Tidak ada mobile app** — semua akses via browser. Halaman publik mobile-responsive, panel admin belum dioptimasi untuk layar sangat kecil.

2. **Tidak ada SSO** — sistem autentikasi sendiri, tidak terintegrasi dengan akun Google Workspace atau LDAP sekolah.

3. **Integrasi MikroTik tidak langsung** — membutuhkan Bot API eksternal (Python/FastAPI) yang berjalan di jaringan sekolah.

4. **WhatsApp via Baileys** — bergantung pada sesi WhatsApp yang tetap aktif. Jika sesi expired, notifikasi berhenti sampai scan QR ulang.

5. **File storage lokal** — tidak menggunakan cloud storage (S3 dll). Backup file perlu dilakukan manual atau via cron eksternal.

6. **Finance module terpisah** — data keuangan tidak terintegrasi dengan data lab (misal tidak ada laporan gabungan otomatis).

---

## Roadmap

### v2.1 (Current — Agustus 2026)
- [x] Jurnal lab dengan foto
- [x] Refactor booking ke partial-based view + AJAX navigation
- [x] Realtime update booking via WebSocket
- [x] File manager controller (backend siap, view pending)
- [x] Fix upload limit (Nginx + PHP + Laravel konsisten)
- [x] Perbaikan tema halaman submit tugas
- [x] Halaman barang rusak lintas lab dengan form perbaikan inline
- [x] Badge counter barang rusak di sidebar dan inventaris admin
- [x] Realtime tugas via Reverb WebSocket (submit, toggle akses, nilai, download toggle)
- [x] Desain ulang halaman tugas siswa (card deadline besar, strip warna, countdown per detik)
- [x] Security fix: semua endpoint admin tugas dilindungi access control + isolasi per guru
- [x] Fix submit ulang: tugas biasa tanpa reopen sekarang dicek duplikat nama
- [x] Download submission oleh siswa (toggle oleh guru, realtime)
- [x] Fix download soal 403: dipindahkan ke public controller, guard PIN kelas
- [x] Export nilai ke Excel (.xls dengan warna otomatis)
- [x] Download ZIP semua submission per kelas

### v2.2 (Q3 2026)
- [ ] File manager view lengkap (halaman admin)
- [ ] Export jurnal ke PDF
- [ ] Notifikasi WA saat booking di-approve/reject
- [ ] Dashboard analytics: grafik penggunaan lab per bulan

### v2.3 (Q4 2026)
- [ ] Import jadwal tetap dari Excel
- [ ] API endpoint untuk integrasi bot WA (cek jadwal via WA)
- [ ] Backup otomatis file storage via scheduled command

### v3.0 (2027)
- [ ] SSO via Google Workspace
- [ ] Mobile PWA untuk teknisi
- [ ] Sistem antrian booking (prioritas berdasarkan urgency)
- [ ] Integrasi absensi siswa dengan jadwal lab
