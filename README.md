# 🖥️ Lab Management System

**Sistem Informasi Manajemen Laboratorium Komputer**
*Nuris Jember*

---

## 📋 Tentang Proyek

Lab Management System adalah aplikasi web berbasis Laravel untuk mengelola penggunaan laboratorium komputer di lingkungan sekolah. Sistem ini mencakup jadwal & booking lab (real-time), kontrol internet lab via MikroTik, pengumpulan tugas siswa tanpa login, manajemen inventaris, modul keuangan (finance), serta notifikasi WhatsApp otomatis.

---

## ✨ Fitur Utama

### 🗓️ Jadwal & Booking
- Jadwal tetap mingguan per lab, dengan update real-time via WebSocket (Reverb)
- Booking lab oleh guru (tanpa login) dengan verifikasi nama & HP
- Booking khusus hari Minggu (Sunday Booking)
- Multi-slot booking (pilih beberapa slot sekaligus)
- Pengecekan konflik jadwal otomatis
- Approve booking tunggal atau grup (semua slot sekaligus)
- Notifikasi WA otomatis saat booking disetujui

### 🌐 Kontrol Internet Lab (MikroTik)
- Token akses unik per sesi (format: `XXXX-XXXX`)
- Link kontrol dikirim via WhatsApp ke guru
- Hidupkan/matikan internet lab dari HP tanpa login
- Monitoring perangkat yang terhubung ke lab
- Auto-generate token beberapa menit sebelum jadwal rutin
- Auto-invalidate token setelah sesi berakhir

### 📚 Pengumpulan Tugas
- Guru buat tugas dengan token/PIN khusus (tanpa login)
- Upload file lampiran soal untuk didownload siswa
- Siswa kumpul tugas tanpa perlu login
- Filter tugas per lembaga & kelas
- Guru beri nilai & feedback per submission

### 📦 Inventaris & Laporan
- Manajemen inventaris & log maintenance lab
- Laporan penggunaan lab, inventaris, dan rekap (export PDF)
- Halaman publik untuk melihat inventaris & rekap tanpa login

### 💰 Modul Keuangan (Finance)
- Dashboard, manajemen transaksi, dan budget terpisah dari modul lab
- Laporan keuangan (Laporan Controller)
- Autentikasi & manajemen user tersendiri untuk modul finance

### 👥 Manajemen User & Akses
- Role & permission berbasis `spatie/laravel-permission` (Admin, Operator, Guru)
- Operator dibatasi akses per lab
- Database guru (Teacher) dengan autocomplete
- Manajemen organisasi/sekolah (multi-lembaga)

### 📊 Monitoring & Observability
- Laravel Horizon — dashboard & monitoring queue worker
- Laravel Telescope — debugging request, query, job, dan exception
- Metrics Prometheus (`spatie/laravel-prometheus`) untuk monitoring aplikasi

---

## 🛠️ Teknologi

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 10 (PHP 8.1+, image Docker pakai PHP 8.3) |
| Frontend | Blade + Livewire 3 + Alpine.js + Tailwind CSS |
| Database | PostgreSQL |
| Cache / Session / Queue | Redis |
| Real-time | Laravel Reverb (WebSocket) + Laravel Echo + Pusher JS |
| Queue Worker | Laravel Horizon |
| Auth API | Laravel Sanctum |
| Otorisasi | Spatie Laravel Permission |
| Observability | Laravel Telescope, Spatie Laravel Prometheus |
| PDF | barryvdh/laravel-dompdf |
| Chart | Chart.js |
| WhatsApp | Baileys (primary) + Fonnte (fallback), via bot Python terpisah |
| MikroTik | PHP Socket API + Python Flask Proxy |
| Container | Docker (multi-stage build) + Nginx + PHP-FPM |
| Deployment | Docker Compose (dev/prod) / Docker Swarm |

---

## 📁 Struktur Modul Penting

```
app/
├── Http/Controllers/
│   ├── BookingController.php
│   ├── ScheduleController.php / ScheduleAdminController.php
│   ├── LabControlController.php
│   ├── AssignmentAdminController.php / AssignmentPublicController.php
│   ├── InventoryAdminController.php / InventoryPublicController.php
│   ├── Finance/  (DashboardController, TransactionController, BudgetController, ...)
│   └── ...
├── Models/
│   ├── Booking.php, SundayBooking.php, Schedule.php, LabSession.php
│   ├── Teacher.php, Organization.php, LabClass.php
│   ├── Assignment.php, AssignmentSubmission.php
│   ├── LabInventory.php, InventoryMaintenanceLog.php
│   ├── Finance/  (model modul keuangan)
│   └── ...
├── Services/
│   ├── Booking/  (ConflictCheckerService, BookingApprovalService, ...)
│   ├── Schedule/ (ScheduleAvailabilityService, BookingSubmissionService, ...)
│   ├── MikroTikService.php, LabControlService.php
│   ├── WhatsAppService.php, InventoryService.php
│   └── DashboardService.php, RekapService.php, TransactionService.php
├── Jobs/
└── Events/
```

---

## ⚙️ Instalasi

Proyek ini bisa dijalankan dengan **Docker** (direkomendasikan, sudah termasuk Nginx, Reverb, dan Vite dev server) atau secara **manual**.

### 1. Clone Repository
```bash
git clone https://github.com/rosy746/lab-management.git
cd lab-management
```

### 2. Konfigurasi Environment
```bash
cp .env.example .env
```
Sesuaikan minimal variabel berikut di `.env`:
```env
APP_URL=http://localhost
APP_TIMEZONE=Asia/Jakarta

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1        # atau "postgres" jika pakai Docker
DB_DATABASE=lab_management
DB_USERNAME=postgres
DB_PASSWORD=your_password

REDIS_HOST=127.0.0.1     # atau "redis" jika pakai Docker

REVERB_APP_ID=lab-management
REVERB_HOST=127.0.0.1
REVERB_PORT=8080

# MikroTik & Bot WhatsApp Python
MIKROTIK_HOST=your_mikrotik_ip
MIKROTIK_PORT=your_mikrotik_port
MIKROTIK_USER=your_mikrotik_user
MIKROTIK_PASS=your_mikrotik_password

BOT_URL=http://IP_BOT:5000
BOT_WEBHOOK_URL=http://IP_BOT:5000/api/webhook/lab-session
```

> ⚠️ **Catatan:** file `env.example` di root repo saat ini masih berisi kredensial contoh dari project lain (host/IP & password MikroTik yang tampak asli) dan nama database `eduzone`. Sebaiknya file ini dibersihkan/diganti placeholder sebelum di-commit ulang, dan gunakan `.env.example` sebagai acuan utama karena sudah sesuai project ini.

---

### 🐳 Opsi A — Instalasi via Docker (Direkomendasikan)

**Prasyarat:** Docker & Docker Compose, network eksternal `network` (dipakai bareng service lain seperti Postgres/Redis).

```bash
# Buat network eksternal jika belum ada
docker network create network

# Siapkan secret untuk Reverb app key (dipakai saat build asset)
mkdir -p secrets
echo "your-reverb-app-key" > secrets/vite_reverb_app_key.txt

# Build & jalankan container (app, vite, nginx, queue/horizon, reverb)
docker compose up -d --build

# Generate app key, migrate, dan seed
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link
```

Aplikasi dapat diakses di `http://localhost:${NGINX_PORT:-8083}` (sesuaikan dengan `NGINX_PORT` di `.env`).

Ada juga `Makefile` berisi shortcut command Docker (`dev-up`, `migrate`, `seed`, `shell`, dll) — sesuaikan nama file compose di dalamnya (`COMPOSE_FILE`) dengan `docker-compose.yml` yang tersedia di repo ini sebelum dipakai, atau jalankan langsung dengan `docker compose ...` seperti contoh di atas.

Untuk deployment skala lebih besar tersedia juga `docker-compose.swarm.yml` (Docker Swarm) dan `infrastructure/docker-compose.swarm.yml`.

---

### 💻 Opsi B — Instalasi Manual

**Prasyarat:** PHP 8.1+ (disarankan 8.3), Composer, Node.js 20+, PostgreSQL, Redis.

```bash
composer install
npm install && npm run build

php artisan key:generate
php artisan migrate --seed
php artisan storage:link
```

Jalankan proses pendukung di terminal terpisah (atau via Supervisor/systemd di server):
```bash
php artisan horizon           # queue worker
php artisan reverb:start      # WebSocket server
php artisan schedule:work     # scheduler (khusus dev; gunakan cron di produksi)
```

**Crontab (Scheduler) — untuk produksi manual:**
```bash
crontab -e
# Tambahkan:
* * * * * cd /path/to/lab-management && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔄 Update / Deploy Ulang

**Docker:**
```bash
git pull
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan config:clear
docker compose exec app php artisan cache:clear
docker compose exec app php artisan view:clear
```

**Manual:**
```bash
git pull
composer install --no-dev --optimize-autoloader
npm run build
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

---

## 🧪 Testing

```bash
php artisan test
# atau
./vendor/bin/phpunit
```

---

## 🔐 Catatan Keamanan

- File `.env` (beserta isi kredensial asli) **tidak boleh** disertakan di repository.
- File `env.example` di root saat ini memuat nilai yang tampak seperti kredensial asli (IP & password MikroTik) — sebaiknya diganti placeholder dan divalidasi ulang sebelum push ke remote publik.
- Kredensial MikroTik & database disimpan di `.env`, bukan hardcode di kode.
- Reverb app key untuk build asset di-mount sebagai Docker BuildKit secret (`secrets/vite_reverb_app_key.txt`), bukan ARG/ENV biasa, agar tidak tersimpan permanen di layer image.
- Token WA disimpan & dikelola di bot Python terpisah.
- Rate limiting (`throttle`) aktif pada endpoint publik seperti booking, submit tugas, dan verifikasi PIN.
- Laravel Telescope aktif (`TELESCOPE_ENABLED=true`) — pastikan dibatasi aksesnya atau dimatikan di lingkungan produksi publik.

---

## 📞 Kontak

**Nuris Jember** — Sistem Informasi Laboratorium Komputer