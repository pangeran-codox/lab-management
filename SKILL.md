---
name: lab-management
description: Panduan kerja untuk codebase Laravel "Lab Management System" (Nuris Jember) — sistem jadwal/booking lab komputer, kontrol internet MikroTik, pengumpulan tugas, inventaris, dan modul keuangan. Gunakan skill ini setiap kali mengerjakan perubahan, bugfix, fitur baru, migrasi, atau setup environment di project ini — termasuk hal terkait booking/schedule, MikroTik lab control, notifikasi WhatsApp, real-time (Reverb), queue (Horizon), modul finance, atau konfigurasi Docker project ini. Baca ini SEBELUM eksplorasi ulang struktur project dari nol.
---

# Lab Management System — Panduan Codebase

Laravel 10 (PHP 8.1+/8.3) monolith untuk manajemen lab komputer sekolah. Blade + Livewire 3 + Alpine.js + Tailwind di frontend, PostgreSQL + Redis di backend, real-time via Laravel Reverb.

## Peta Arsitektur Cepat

Project ini pakai pola **thin controller → Service class**. Logika bisnis hidup di `app/Services/`, bukan di controller. Saat menambah fitur, ikuti pola ini — jangan taruh logika kompleks langsung di controller atau model.

```
app/
├── Http/Controllers/       # thin controllers, banyak yang public (tanpa login) & admin (auth)
│   └── Finance/             # sub-modul finance, TERPISAH otentikasi & DB-nya sendiri
├── Models/
│   └── Finance/
├── Services/
│   ├── Booking/             # ConflictCheckerService, BookingApprovalService, BookingQueryService, BookingAccessService
│   ├── Schedule/             # ScheduleAvailabilityService, BookingSubmissionService, ScheduleQueryService
│   ├── MikroTikService.php  # komunikasi socket API ke router MikroTik
│   ├── LabControlService.php# generate/validate token kontrol internet
│   ├── WhatsAppService.php  # kirim notifikasi via bot Python (Baileys/Fonnte)
│   ├── InventoryService.php, DashboardService.php, RekapService.php, TransactionService.php
├── Jobs/                    # queue jobs (dijalankan via Horizon)
├── Events/                  # ScheduleUpdated, BookingCreated → broadcast via Reverb
├── Console/Commands/        # MergeDuplicateTeachers, GenerateClassPins
└── Providers/
    ├── BroadcastServiceProvider.php, HorizonServiceProvider.php, TelescopeServiceProvider.php
```

### Modul & tanggung jawabnya
| Modul | Controller utama | Catatan |
|---|---|---|
| Jadwal & Booking | `ScheduleController`, `ScheduleAdminController`, `BookingController` | Ada jalur publik tanpa login (guru booking via verifikasi nama+HP) dan jalur admin. Broadcast real-time via event `BookingCreated`/`ScheduleUpdated`. |
| Kontrol Internet | `LabControlController` + `LabControlService` + `MikroTikService` | Akses via token unik `{token}`, tanpa login. Token auto-generate/invalidate terjadwal (lihat scheduler). |
| Tugas | `AssignmentAdminController`, `AssignmentPublicController` | Siswa akses via PIN kelas, tanpa akun. |
| Inventaris | `InventoryAdminController`, `InventoryPublicController`, `InventoryMaintenanceController`, `InventoryReportController` | |
| Finance | `app/Http/Controllers/Finance/*` | Modul terpisah: auth, user, dashboard, transaction, budget, laporan sendiri. Bisa pakai DB berbeda (`DB_FINANCE_*` di `.env`). |
| User & Akses | `UserController`, `OrganizationController` | Role/permission via `spatie/laravel-permission` (Admin, Operator, Guru). Operator dibatasi per lab. |

### Rute publik vs terautentikasi
`routes/web.php` dibagi jelas: bagian atas (rute publik, tanpa `auth` middleware) dan bagian dalam `Route::middleware(['auth'])->group(...)` (admin). **Saat menambah endpoint baru, tentukan dulu apakah ini publik (guru/siswa tanpa akun) atau admin**, lalu taruh di grup yang sesuai — jangan taruh endpoint admin di luar grup `auth`.

Endpoint publik yang menerima input (booking, submit tugas, verifikasi PIN, toggle internet) semuanya pakai `throttle:X,1` — ikuti pola ini untuk endpoint publik baru guna mencegah abuse.

`routes/finance.php` adalah grup rute terpisah untuk modul keuangan.

## Real-time & Queue

- **Broadcasting**: `BROADCAST_CONNECTION=reverb`. Event penting: `BookingUpdated`/`ScheduleUpdated`/`BookingCreated` — cek `app/Events/` sebelum menambah broadcast baru, dan daftarkan channel-nya di `routes/channels.php`.
- **Queue**: `QUEUE_CONNECTION=redis`, worker dijalankan via **Laravel Horizon** (`php artisan horizon`, atau container `queue` di Docker). Job baru masuk ke `app/Jobs/`.
- **Scheduler**: token MikroTik auto-generate H-5 menit & auto-invalidate setelah sesi — logic ini ada di `app/Console/Kernel.php` / scheduled commands. Cron wajib jalan (`schedule:run` tiap menit) di server manual; di Docker pastikan container scheduler/cron berjalan.

## Integrasi Eksternal

- **MikroTik**: `MikroTikService` bicara ke router via PHP Socket API (bukan REST). Kredensial di `MIKROTIK_HOST/PORT/USER/PASS`. Ada juga Python Flask proxy terpisah (di luar repo Laravel ini) untuk sebagian operasi.
- **WhatsApp**: `WhatsAppService` mengirim ke bot Python eksternal (`BOT_URL`, `BOT_WEBHOOK_URL`) yang pakai Baileys (utama) dengan fallback Fonnte. Bot ini **bukan bagian dari repo ini** — treat sebagai service eksternal saat debugging masalah notifikasi tidak terkirim.

## Environment & Database

- DB utama: **PostgreSQL** (`DB_CONNECTION=pgsql`, DB `lab_management`). Modul finance bisa pakai DB terpisah lewat `DB_FINANCE_*`.
- Cache/session/queue: **Redis**, dengan `REDIS_DB`/`REDIS_CACHE_DB` terpisah — kalau nambah service Redis baru di lingkungan yang sama, pastikan index DB tidak bentrok dengan project lain yang share Redis instance yang sama.
- ⚠️ File `env.example` (root, tanpa titik) berbeda dari `.env.example` dan masih memuat sisa konfigurasi/kredensial dari project lain (`eduzone`, IP MikroTik yang tampak asli). **Selalu pakai `.env.example` sebagai acuan**, dan jangan copy nilai dari `env.example` mentah-mentah. Idealnya file `env.example` dibersihkan atau dihapus.
- Reverb app key untuk build asset Docker di-mount sebagai BuildKit secret (`secrets/vite_reverb_app_key.txt`), bukan ENV/ARG biasa — ikuti pola ini kalau menambah secret build-time baru.

## Menjalankan Project

**Docker (utama):**
```bash
docker compose up -d --build
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan tinker      # debug
docker compose logs -f app                      # logs
```
Services: `app` (PHP-FPM), `vite` (dev server hot reload), `nginx` (reverse proxy), `queue` (Horizon), `reverb` (WebSocket).
Catatan: `Makefile` yang ada di repo mereferensikan `docker-compose.dev.yml`/`docker-compose.prod.yml` yang **tidak ada** di repo ini (peninggalan template project lain) — pakai `docker compose -f docker-compose.yml ...` langsung, atau perbaiki Makefile-nya dulu sebelum dipakai.

**Manual:**
```bash
composer install && npm install && npm run build
php artisan migrate --seed
php artisan horizon        # terminal terpisah
php artisan reverb:start   # terminal terpisah
```

**Testing:**
```bash
php artisan test           # tests/Unit & tests/Feature
```

## Konvensi Kode

- Gunakan **Service class** untuk logika bisnis (lihat `app/Services/Booking/` dan `app/Services/Schedule/` sebagai contoh pemisahan tanggung jawab: query vs approval vs conflict-checking).
- Endpoint publik tanpa login → selalu tambahkan `throttle` middleware.
- Perubahan skema → migration Laravel biasa (`database/migrations`), bukan edit langsung `schema.sql`/`lab_management.sql`/`finance.sql` di root (file-file itu tampaknya dump/referensi, bukan sumber kebenaran).
- Role/permission baru → definisikan lewat `spatie/laravel-permission`, jangan hardcode pengecekan role di controller.
- Format kode: `laravel/pint` tersedia sebagai dev dependency — jalankan `./vendor/bin/pint` sebelum commit kalau ada perubahan signifikan.

## Yang Perlu Diwaspadai

- Telescope aktif secara default (`TELESCOPE_ENABLED=true`) — jangan expose ke publik di production tanpa autentikasi tambahan.
- Modul finance punya siklus auth & user sendiri, terpisah dari user lab — jangan asumsikan `User` model yang sama dipakai di kedua modul tanpa cek ulang.
- `env.example` (root) berisi data yang tampak seperti kredensial asli — jangan jadikan sumber nilai saat setup environment baru, dan tandai untuk dibersihkan jika terlihat lagi.
