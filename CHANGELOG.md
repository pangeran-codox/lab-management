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

## [2.1.5] — 2026-08-11

### Added
- **Download submission oleh siswa** — siswa bisa download ulang file yang sudah dikumpulkan, akses dikontrol guru
  - Kolom `allow_student_download` (boolean, default false) di tabel `assignments`
  - Migration: `add_allow_student_download_to_assignments_table`
  - Panel guru (Tab Aksi Tugas): card baru "📥 Download Siswa" — toggle buka/tutup akses
  - Default: **Nonaktif** — siswa tidak bisa download sampai guru aktifkan
  - Saat aktif: banner hijau + tombol "Download" muncul di tiap baris submission di halaman siswa
  - Saat dinonaktifkan: banner + tombol hilang **realtime via WebSocket** tanpa siswa perlu reload
  - Guard keamanan: cukup PIN kelas valid di session, tidak perlu input nama siswa
  - Route baru: `POST /tugas-admin/{assignment}/toggle-student-download` → `assignment.toggle-student-download`
  - Route baru: `GET /tugas/{assignment}/submission/{submission}/download` → `assignment.submission.download-own`
  - Payload event `AssignmentUpdated` diperkaya dengan field `allow_student_download` agar frontend bisa sinkronisasi realtime

### Fixed
- **Download soal (403 Forbidden)** — route `/tugas/{assignment}/download-attachment` sebelumnya diarahkan ke `AssignmentAdminController` yang memerlukan token guru, sehingga siswa mendapat 403 saat mencoba download soal
  - Dipindahkan ke `AssignmentPublicController::downloadAttachment()` — cukup validasi PIN kelas di session
  - Jika belum input PIN → redirect ke halaman PIN dengan pesan yang jelas
  - Jika PIN valid tapi kelas tidak cocok → redirect + error
  - Jika file tidak ada di storage → error yang informatif

---

## [2.1.4] — 2026-08-09

### Added
- **Realtime tugas via Reverb WebSocket** — fitur tugas kini tanpa reload
  - Event `AssignmentSubmitted` — broadcast ke channel `assignments.{id}` saat siswa submit
  - Event `AssignmentUpdated` — broadcast ke dua channel: `assignments.{id}` (panel guru) dan `class_assignments.{class_slug}` (halaman siswa)
  - Panel guru: baris submission baru muncul langsung di tabel + toast "📥 NamaSiswa mengumpulkan" tanpa reload
  - Halaman siswa (`/tugas`): kartu tugas baru muncul otomatis saat guru buka akses, kartu fade-out saat guru tutup akses
  - Halaman show siswa: daftar yang sudah mengumpulkan update realtime, banner muncul saat akses ditutup atau deadline diubah guru
  - Panel guru: badge nilai update realtime setelah guru simpan nilai

### Changed
- **Desain ulang halaman tugas siswa (`/tugas`)** — card baru yang jauh lebih jelas
  - Strip warna di atas card: hijau (aktif), merah (mendesak <24 jam), abu (ditutup)
  - Deadline block besar di tengah card: nama hari, tanggal lengkap, jam, countdown realtime per detik
  - Tombol "Kumpulkan Sekarang" penuh lebar, mencolok — tidak bisa terlewat
  - Chip info: jumlah yang sudah kumpul, badge pertemuan series, ada soal terlampir, cuplikan deskripsi
  - Badge status pojok kanan atas dengan titik berkedip saat aktif

### Fixed
- **Celah keamanan kritis** — semua method di `AssignmentAdminController` sebelumnya tidak ada access control
  - `destroy`, `update`, `gradeSubmission`, `destroySubmission`, `downloadSubmission`, `downloadZip`, `exportExcel`, `downloadAttachment` sekarang memanggil `authorizeAccess()` + `authorizeAssignment/Submission()`
  - Guru A tidak bisa lagi akses/hapus tugas atau submission milik Guru B
  - Request tanpa token valid di-abort 403
- **Celah submit ulang** — tugas biasa (tanpa reopen) sebelumnya tidak dicek duplikat nama, siswa bisa submit berkali-kali
  - Sekarang selalu cek `hasAlreadySubmitted()` terlepas dari ada tidaknya `open_period`

---

## [2.1.3] — 2026-08-09

### Added
- **Pilihan ukuran kertas di semua editor laporan** — custom dropdown yang tampil konsisten di semua browser (tidak lagi pakai `<select>` native yang tidak bisa di-style di toolbar gelap)
  - Tersedia di 4 editor: Inventaris, Jurnal Lab, Rekap Penggunaan, Jadwal
  - Pilihan: A4/A3/A5 Portrait & Landscape, Letter Portrait & Landscape, Legal Portrait & Landscape
  - Default: A4 Portrait (inventaris, rekap) dan A4 Landscape (jurnal, jadwal)
  - Pilihan aktif di-highlight hijau, ikon portrait (biru) dan landscape (ungu)
  - Menutup otomatis saat klik di luar dropdown
  - `@page` CSS dikontrol via `<style id="page-style">` sehingga perubahan langsung terlihat di print preview tanpa reload

---

## [2.1.2] — 2026-08-08

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
