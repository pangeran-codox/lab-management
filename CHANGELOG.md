# Changelog — Lab Management System

Semua perubahan signifikan pada project ini didokumentasikan di file ini.

Format mengikuti [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

---

## [Unreleased]

### Planned
- File Manager: halaman admin lengkap untuk kelola foto jurnal dan file tugas
- Export jurnal ke PDF
- Notifikasi WA saat booking di-approve/reject
- Dashboard analytics: grafik penggunaan lab per bulan

---

## [2.1.1] — 2026-07-25

### Added
- **Halaman Barang Rusak** (`GET /inventaris-admin/rusak`) — dashboard terpusat untuk mengelola semua barang rusak dari seluruh laboratorium
  - 4 stat cards: total unit rusak, jumlah jenis barang terdampak, jumlah lab terdampak, total unit yang sudah diperbaiki
  - Filter pills per lab — klik langsung memfilter ke lab tertentu
  - Filter pencarian (nama/merk/model) dan filter kategori
  - Per-item detail: breakdown qty (total/baik/rusak/cadangan), progress bar persentase unit baik, riwayat 3 perbaikan terakhir
  - Form perbaikan inline (accordion per item): isi jumlah diperbaiki, jenis perbaikan, biaya, deskripsi
  - Submit form otomatis: pindahkan unit rusak → baik, update kondisi otomatis berdasarkan rasio, catat ke `inventory_maintenance_logs`
  - Empty state khusus saat semua inventaris dalam kondisi baik
- **Tombol "Barang Rusak"** di actions bar halaman inventaris admin — warna merah dengan badge counter jumlah total unit rusak
- **Menu "Barang Rusak"** di sidebar admin — badge merah menampilkan total unit rusak dari semua lab yang bisa diakses user
- **Route baru:**
  - `GET  /inventaris-admin/rusak` — `inventory.broken`
  - `POST /inventaris-admin/{inventory}/perbaiki` — `inventory.mark-fixed`
- **Method baru di `InventoryAdminController`:**
  - `brokenItems()` — query `quantity_broken > 0`, eager load maintenanceLogs, group by lab
  - `markFixed()` — validasi, DB transaction, update qty, auto-update kondisi, catat maintenance log
- Import jadwal tetap dari Excel

---

## [2.1.0] — 2026-07-24

### Added
- **Jurnal Lab** — fitur baru untuk pencatatan penggunaan harian lab
  - Form isi jurnal dengan upload foto (max 5 foto, 5MB per foto)
  - Drag-and-drop dropzone dengan preview sebelum submit
  - Foto ditampilkan langsung di tabel jadwal (140×105px) untuk preview cepat
  - Scroll horizontal kalau foto lebih dari 2
  - Navigasi tanggal (prev/next)
  - Validasi eligibility: jurnal hanya bisa diisi dalam window waktu valid
  - Slot berurutan dari guru/kegiatan yang sama digabung otomatis (group)
- **File Manager** — controller `FileManagerController` untuk kelola semua file upload
  - List foto jurnal dari disk `public` dan file tugas dari disk `local`
  - Hitung total kapasitas per tipe dan grand total
  - Deteksi file orphan (ada di DB tapi tidak ada di storage)
  - Download foto jurnal langsung oleh admin
  - Hapus foto jurnal (file + record DB)
- **Booking partial-based view** — refactor besar halaman booking
  - Pecah `index.blade.php` jadi 4 partials: `weekly-table`, `weekly-header`, `filter-bar`, `booking-list`
  - `weekly-header` baru: tombol prev/next/hari-ini mengganti HTML week picker
  - Navigasi minggu via AJAX (`fetch()`) tanpa reload halaman penuh
  - URL browser di-update via `history.pushState` (bisa di-bookmark)
  - Tombol back/forward browser ditangani via `popstate` event
  - Tambah endpoint `GET /booking/weekly-grid` untuk AJAX partial swap
- **WebSocket realtime booking** — tabel mingguan booking update otomatis
  - Subscribe `channel('schedules').listen('.schedule.updated')` di `booking.js`
  - Hanya reload kalau event tanggal ada dalam range minggu yang ditampilkan
  - Debounce 600ms untuk hindari flood request saat burst event
- **Fix Vite HMR di Docker** — file system polling untuk Windows host
  - Tambah `watch: { usePolling: true, interval: 800, ignored: [...] }` di `vite.config.js`
  - Tambah `VITE_HMR_HOST` environment variable ke service `vite` di `docker-compose.yml`
  - Fix `public/hot` berisi `localhost` padahal harusnya domain eksternal
- **Nginx upload limit** — `client_max_body_size 25M` di `default.conf` dan `default.swarm.conf`

### Changed
- **Tugas siswa (`show.blade.php`)** — selaraskan tema warna dengan halaman daftar tugas
  - Migrate dari inline CSS (#1A2517 dark forest green) ke `assignment.css` (#00693E Dartmouth green)
  - Ganti standalone HTML ke `@extends('layouts.public-schedule')`
  - Tambah class `.show-*` di `assignment.css` untuk semua komponen halaman submit
  - Teks hint upload diperbarui ke "Maks 5MB"
- **Batas upload file tugas** — turunkan dari 10MB ke 5MB
  - `AssignmentPublicController`: `max:10240` → `max:5120`
  - Pesan error: "Ukuran file maksimal 5MB"
- **Foto jurnal** — batas ditambahkan
  - Server: validasi `photos.*.max:5120` (5MB per foto) + `photos.max:5` (max 5 foto)
  - JS: `slice(0, 5)` (sebelumnya 10)
  - UI hint sudah benar "Maksimal 5 foto" (tidak berubah)
- **Upload foto jurnal — bug fixes**
  - File foto di-upload sebelum transaksi DB → sekarang ada `try/catch` dengan cleanup otomatis
  - `journal->photos()->delete()` tidak hapus file storage → sekarang hapus file dulu sebelum delete record
  - `$photo->store()` bisa return `false` → sekarang dicek dengan throw exception

### Fixed
- Error `Cannot set properties of null (setting 'value')` di halaman jurnal
  - Root cause: `public/hot` berisi `localhost:5173` sehingga browser memuat JS dari host lokal, bukan dari Vite container
  - Fix: `VITE_HMR_HOST` di-pass sebagai environment variable ke container `lab_vite`

---

## [2.0.0] — 2026-06-15

### Added
- **Modul Finance** — sistem keuangan lab terpisah dengan database sendiri
  - CRUD transaksi pemasukan dan pengeluaran
  - Manajemen anggaran (budget) per kategori
  - Dashboard keuangan dengan grafik
  - Laporan keuangan dengan filter periode
  - Notifikasi WA via Baileys untuk setiap transaksi
  - Alert budget warning saat penggunaan mencapai threshold
  - Guard autentikasi terpisah (`FinanceUser`, bukan `User`)
- **MikroTik dynamic config** — konfigurasi disimpan di database
  - Model `MikroTikDevice` dan `MikroTikLab` mengganti config hardcode di `.env`
  - Halaman admin untuk kelola device dan mapping lab di `/settings/mikrotik`
  - Cache lab map di Redis (1 jam TTL)
- **Lab Control via token** — teknisi kontrol internet lab tanpa login
  - Link unik per lab dengan token yang bisa di-generate ulang
  - Tampilkan status realtime (online/offline + jumlah perangkat aktif)
- **Halaman Rekap Publik** — rekap penggunaan lab, export PDF
- **Important Schedule** — jadwal penting yang memblokir slot di jadwal publik
- **Laporan Penggunaan Lab** — admin bisa generate laporan penggunaan per periode
- **Docker Swarm support** — `docker-compose.swarm.yml` untuk production deployment
  - 2 replicas PHP-FPM (rolling update)
  - Scheduler container (cron loop)
  - Dynamic DNS resolver di Nginx untuk container re-scheduling

### Changed
- Upgrade Laravel 9 → Laravel 10
- Upgrade PHP 8.1 → PHP 8.3
- Ganti MySQL ke PostgreSQL (dual database: lab_management + finance)
- Vite 4 → Vite 5
- Session driver: file → Redis
- Cache driver: file → Redis

---

## [1.5.0] — 2026-03-01

### Added
- **Tugas Siswa** — fitur pengumpulan tugas dengan PIN kelas
  - PIN 6 digit per kelas, generate otomatis
  - Form upload file (pdf, doc, docx, dll)
  - Portal admin guru via token (tanpa akun sistem)
  - Grading dan feedback dari guru
- **Booking Hari Minggu** — booking full day dengan alur terpisah
- **Session Booking** — beberapa slot dalam satu sesi bisa di-approve/reject sekaligus
- **Inventory Maintenance Log** — catat perbaikan/maintenance per item
- **Export PDF** — inventaris dan rekap bisa diexport ke PDF via DomPDF

### Fixed
- Konflik booking tidak terdeteksi saat dua user submit bersamaan (race condition via DB unique constraint)
- N+1 query di halaman jadwal publik (tambah eager loading)

---

## [1.0.0] — 2025-10-01

### Added
- **Jadwal Lab** — tampilan mingguan jadwal tetap semua lab
- **Booking Lab** — form booking dari halaman publik, approval workflow
- **Inventaris** — CRUD aset lab per laboratorium
- **Panel Admin** — dashboard, CRUD jadwal, kelola guru, sekolah, kelas
- **WebSocket (Reverb)** — update realtime saat booking baru
- **WhatsApp notification** — notifikasi booking ke petugas
- **Autentikasi** — login dengan role (admin, operator, teknisi)
- **RBAC** — Spatie Permission, akses berbasis role
- **Docker** — multi-stage Dockerfile + docker-compose dev

---

## Format Versi

`MAJOR.MINOR.PATCH`

- `MAJOR` — perubahan breaking, redesign besar
- `MINOR` — fitur baru backward-compatible
- `PATCH` — bug fix, perubahan kecil

[Unreleased]: https://github.com/your-org/lab-management/compare/v2.1.0...HEAD
[2.1.0]: https://github.com/your-org/lab-management/compare/v2.0.0...v2.1.0
[2.0.0]: https://github.com/your-org/lab-management/compare/v1.5.0...v2.0.0
[1.5.0]: https://github.com/your-org/lab-management/compare/v1.0.0...v1.5.0
[1.0.0]: https://github.com/your-org/lab-management/releases/tag/v1.0.0
