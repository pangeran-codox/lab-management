# Lab Management System

Sistem manajemen laboratorium komputer untuk SMKS Nuris Jember. Mengelola jadwal pemakaian lab, booking, inventaris, jurnal penggunaan, pengumpulan tugas siswa, keuangan, dan kontrol internet via MikroTik — semuanya dalam satu platform.

![Laravel](https://img.shields.io/badge/Laravel-10.x-red?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3-blue?logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-blue?logo=postgresql)
![Docker](https://img.shields.io/badge/Docker-Swarm-blue?logo=docker)
![License](https://img.shields.io/badge/license-MIT-green)

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Prasyarat](#prasyarat)
- [Instalasi Development](#instalasi-development)
- [Konfigurasi Environment](#konfigurasi-environment)
- [Menjalankan Aplikasi](#menjalankan-aplikasi)
- [Struktur Proyek](#struktur-proyek)
- [Modul Sistem](#modul-sistem)
- [Akun Default](#akun-default)
- [Deployment Production](#deployment-production)
- [Dokumentasi Lanjutan](#dokumentasi-lanjutan)

---

## Fitur Utama

| Modul | Deskripsi |
|-------|-----------|
| **Jadwal Lab** | Tampilan mingguan jadwal tetap per lab, update realtime via WebSocket |
| **Booking** | Guru booking lab untuk kegiatan insidental, approval workflow, notif WA |
| **Jurnal Lab** | Pencatatan penggunaan harian lab dengan foto, tanpa login |
| **📋 Tugas Siswa** | Sistem pengumpulan tugas lengkap — lihat detail di bawah |
| **Inventaris** | CRUD aset lab, log perbaikan/maintenance, export PDF |
| **Finance** | Pencatatan pemasukan/pengeluaran keuangan lab, budgeting, laporan |
| **Kontrol Internet** | Toggle akses internet per lab via MikroTik API |
| **Notifikasi WA** | Notifikasi booking dan transaksi keuangan via WhatsApp (Baileys) |
| **File Manager** | Kelola semua file upload (foto jurnal + tugas), hitung kapasitas storage |
| **Laporan** | Rekap penggunaan lab, inventaris, export PDF |

---

## 📋 Fitur Tugas Siswa

Sistem pengumpulan tugas yang lengkap tanpa siswa perlu membuat akun.

### Untuk Siswa (via PIN Kelas)

| Fitur | Keterangan |
|-------|-----------|
| 🔑 **Masuk via PIN** | PIN 6 digit yang diberikan guru, tersimpan di sesi browser |
| 📄 **Card tugas yang jelas** | Strip warna status (hijau/merah/abu), deadline besar dengan countdown realtime per detik |
| ⚡ **Realtime tanpa reload** | Tugas baru muncul otomatis saat guru buka akses, hilang saat ditutup |
| 📥 **Upload file** | PDF, Word, PPT, Excel, ZIP, RAR — maksimal 5MB |
| 🔒 **Anti duplikat** | Sistem menolak submit ulang jika nama sudah ada (kecuali guru izinkan) |
| 📎 **Unduh soal** | Download soal/lampiran dari guru langsung dari card tugas |
| 👥 **Lihat yang sudah kumpul** | Daftar nama siswa yang sudah mengumpulkan, update realtime |
| 🔗 **Tugas berkelanjutan** | Badge "Pertemuan X dari Y" untuk materi yang berlanjut antar sesi |
| 🏆 **Lihat nilai** | Nilai dan feedback dari guru muncul realtime setelah dinilai |

### Untuk Guru (via Token)

| Fitur | Keterangan |
|-------|-----------|
| ✏️ **Buat tugas** | Satu form untuk banyak kelas sekaligus, bisa lampirkan soal |
| 📝 **Edit tugas** | Ubah judul, mapel, deadline, keterangan, ganti soal |
| 🔓 **Buka/tutup akses** | Kontrol kapan siswa bisa lihat dan kumpulkan tugas |
| 📅 **Perpanjang deadline** | Buka ulang pengumpulan dengan deadline baru |
| ⚡ **Tugas lanjutan** | Buat pertemuan berikutnya dalam satu rangkaian materi (series) |
| 📬 **Notifikasi realtime** | Toast muncul saat ada siswa mengumpulkan, tanpa perlu refresh |
| 🎯 **Beri nilai & feedback** | Per siswa, skala 0–100, dengan catatan guru |
| 📦 **Download ZIP** | Semua submission satu kelas dikemas jadi satu file .zip |
| 📊 **Export Excel** | Daftar nama + nilai dengan warna (hijau/kuning/merah) siap cetak |
| 🗑️ **Hapus submission** | Hapus file + record jika siswa salah upload |
| 🗑️ **Hapus tugas** | Beserta semua file submission secara permanen |
| 🔐 **Keamanan** | Guru hanya bisa akses tugasnya sendiri (isolasi per token) |

---

## Tech Stack

### Backend
- **PHP 8.3** + **Laravel 10**
- **PostgreSQL 16** — dual database (lab_management + finance)
- **Redis 7** — cache, session, queue
- **Laravel Horizon** — queue management & monitoring
- **Laravel Reverb** — WebSocket server (realtime update)
- **Laravel Telescope** — debugging & monitoring (dev only)
- **Spatie Laravel Permission** — RBAC roles & permissions
- **Laravel DomPDF** — generate laporan PDF

### Frontend
- **Tailwind CSS** — utility-first CSS framework
- **Alpine.js** — lightweight reactive UI
- **Livewire 3** — server-driven components
- **Vite 5** — asset bundler dengan HMR
- **Laravel Echo + Pusher.js** — WebSocket client

### Infrastructure
- **Docker** + **Docker Compose** — containerization
- **Docker Swarm** — production orchestration
- **Nginx 1.27** — reverse proxy + static file serving
- **Node.js 20** — Vite dev server

### Integrasi Eksternal
- **MikroTik** via Bot API Python — kontrol NAT/internet per lab
- **WhatsApp Baileys** — notifikasi WA
- **Fonnte** — webhook WhatsApp alternatif

---

## Prasyarat

- Docker Engine 24+ & Docker Compose v2
- Git

Untuk development lokal tanpa Docker:
- PHP 8.3+ dengan ekstensi: `pdo_pgsql`, `redis`, `gd`, `zip`, `mbstring`, `intl`
- Composer 2.7+
- Node.js 20+ & npm
- PostgreSQL 16+
- Redis 7+

---

## Instalasi Development

### 1. Clone repository

```bash
git clone https://github.com/your-org/lab-management.git
cd lab-management
```

### 2. Salin file environment

```bash
cp .env.example .env
```

Edit `.env` sesuai konfigurasi lokal. Lihat [Konfigurasi Environment](#konfigurasi-environment).

### 3. Buat file secret Reverb

```bash
mkdir -p secrets
echo "your-reverb-app-key" > secrets/vite_reverb_app_key.txt
```

### 4. Jalankan Docker Compose

```bash
docker compose up -d
```

Container yang akan berjalan:
| Container | Deskripsi | Port |
|-----------|-----------|------|
| `lab_app` | PHP-FPM Laravel | 9000 (internal) |
| `lab_nginx` | Nginx reverse proxy | 8080 |
| `lab_vite` | Vite dev server (HMR) | 5173 |
| `lab_queue` | Laravel Horizon | — |
| `lab_reverb` | WebSocket server | 8084 |

### 5. Setup database

```bash
# Masuk ke container app
docker exec -it lab_app sh

# Jalankan migrasi
php artisan migrate

# Seed data awal (opsional)
php artisan db:seed
```

### 6. Buat storage symlink

```bash
docker exec lab_app php artisan storage:link
```

Aplikasi tersedia di `http://localhost:8080`.

---

## Konfigurasi Environment

Variabel kritis yang wajib dikonfigurasi:

```env
# Aplikasi
APP_URL=http://localhost:8080
APP_KEY=                          # generate dengan: php artisan key:generate

# Database Utama
DB_HOST=postgres
DB_DATABASE=lab_management
DB_USERNAME=laravel
DB_PASSWORD=secret

# Database Finance (opsional, modul terpisah)
DB_FINANCE_HOST=postgres
DB_FINANCE_DATABASE=finance

# Redis
REDIS_HOST=redis
REDIS_PASSWORD=null

# WebSocket (Reverb)
REVERB_APP_KEY=your-key
REVERB_APP_SECRET=your-secret
REVERB_HOST=your-domain.com
REVERB_PORT=8084

# MikroTik Bot
BOT_URL=http://bot-server:5000
BOT_TOKEN=your-bot-token

# WhatsApp (Baileys)
BAILEYS_URL=http://baileys-server:3002
BAILEYS_API_KEY=your-api-key

# Vite HMR (penting untuk Docker)
VITE_HMR_HOST=your-domain.com
```

Lihat [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) untuk konfigurasi production lengkap.

---

## Menjalankan Aplikasi

### Development

```bash
# Start semua service
docker compose up -d

# Lihat log
docker compose logs -f

# Lihat log spesifik container
docker logs lab_vite --tail 20
docker logs lab_app --tail 20

# Masuk ke container
docker exec -it lab_app sh

# Artisan commands
docker exec lab_app php artisan migrate
docker exec lab_app php artisan horizon
docker exec lab_app php artisan reverb:start
```

### Artisan Commands Kustom

```bash
# Generate PIN untuk semua kelas
docker exec lab_app php artisan pins:generate

# Merge duplikasi data guru
docker exec lab_app php artisan teachers:merge-duplicates
```

---

## Struktur Proyek

```
lab-management/
├── app/
│   ├── Console/Commands/      # Artisan commands kustom
│   ├── Events/                # BookingCreated, ScheduleUpdated
│   ├── Http/
│   │   ├── Controllers/       # 24 controllers utama + 7 Finance
│   │   └── Middleware/        # CheckRole, CheckLabAccess, FinanceAuth, dll
│   ├── Models/                # 23+ Eloquent models
│   └── Services/              # Service layer (Booking, Schedule, Journal, dll)
│       ├── Booking/           # BookingQueryService, ApprovalService, dll
│       ├── Journal/           # JournalQueryService, AvailabilityService
│       └── Schedule/          # ScheduleQueryService, dll
├── database/
│   ├── migrations/            # 28 migration files
│   └── seeders/
├── docker/
│   ├── nginx/
│   │   ├── default.conf       # Dev Nginx config
│   │   └── default.swarm.conf # Production Swarm config
│   ├── php/
│   │   ├── php-dev.ini        # PHP config development
│   │   ├── php-prod.ini       # PHP config production (OPcache)
│   │   ├── php-fpm.conf       # PHP-FPM config
│   │   └── entrypoint.sh      # Container startup script
│   └── reverb/
│       └── Dockerfile         # Reverb WebSocket container
├── docs/                      # Dokumentasi lengkap
│   ├── ARCHITECTURE.md
│   ├── PRD.md
│   ├── DEPLOYMENT.md
│   ├── API.md
│   └── CONTRIBUTING.md
├── resources/
│   ├── css/                   # Per-page CSS files
│   ├── js/                    # Per-page JS files
│   └── views/                 # Blade templates
│       ├── assignments/       # Halaman tugas siswa
│       ├── booking/           # Halaman booking + partials
│       ├── journal/           # Halaman jurnal lab
│       ├── admin/             # Halaman admin
│       ├── finance/           # Modul keuangan
│       └── layouts/           # Layout templates
├── routes/
│   ├── web.php                # 60+ web routes
│   ├── finance.php            # Finance module routes
│   └── api.php                # API routes (bot/internal)
├── docker-compose.yml         # Dev stack
├── docker-compose.swarm.yml   # Production Swarm stack
├── Dockerfile                 # Multi-stage build
└── vite.config.js             # Vite + Tailwind config
```

---

## Modul Sistem

### Halaman Publik (tanpa login)
| URL | Deskripsi |
|-----|-----------|
| `/` | Jadwal lab mingguan realtime |
| `/inventaris` | Daftar inventaris lab |
| `/rekap` | Rekap penggunaan lab |
| `/journal` | Jurnal penggunaan harian |
| `/tugas` | 📋 Kumpul tugas siswa (via PIN) — realtime, card deadline besar |
| `/lab-control/{token}` | Kontrol internet lab (via token) |

### Panel Admin (butuh login)
| URL | Deskripsi |
|-----|-----------|
| `/dashboard` | Overview statistik |
| `/booking` | Manajemen booking mingguan |
| `/jadwal-admin` | Kelola jadwal tetap |
| `/inventaris-admin` | Kelola inventaris |
| `/guru` | Data guru |
| `/tugas-admin` | Admin tugas (via token guru) |
| `/file-manager` | Kelola file upload & storage |
| `/settings` | Pengaturan sistem |
| `/settings/mikrotik` | Konfigurasi MikroTik |

### Modul Finance
| URL | Deskripsi |
|-----|-----------|
| `/finance/` | Dashboard keuangan |
| `/finance/transactions` | CRUD transaksi |
| `/finance/budgets` | Anggaran & budget |
| `/finance/laporan` | Laporan keuangan |
| `/finance/wa-settings` | Pengaturan notifikasi WA |

---

## Akun Default

Setelah seeder dijalankan:

| Role | Username | Password |
|------|----------|----------|
| Super Admin | `admin` | `password` |
| Operator | `operator` | `password` |
| Teknisi | `teknisi` | `password` |

> **Ganti password default segera setelah pertama login di production.**

---

## Deployment Production

Lihat [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) untuk panduan lengkap deployment ke Docker Swarm.

Ringkasan cepat:

```bash
# Build image production
docker build --target production \
  --secret id=vite_reverb_app_key,src=secrets/vite_reverb_app_key.txt \
  -t iswant/lab-management:v2.x .

# Push ke registry
docker push iswant/lab-management:v2.x

# Deploy ke Swarm
docker stack deploy -c docker-compose.swarm.yml lab
```

---

## Dokumentasi Lanjutan

| Dokumen | Deskripsi |
|---------|-----------|
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | Arsitektur sistem, diagram, keputusan teknis |
| [docs/PRD.md](docs/PRD.md) | Product Requirements Document |
| [docs/DEPLOYMENT.md](docs/DEPLOYMENT.md) | Panduan deployment production |
| [docs/API.md](docs/API.md) | Referensi API endpoints |
| [docs/CONTRIBUTING.md](docs/CONTRIBUTING.md) | Panduan kontribusi & development |
| [CHANGELOG.md](CHANGELOG.md) | Riwayat perubahan |

---

## Lisensi

MIT License — lihat file [LICENSE](LICENSE) untuk detail.
