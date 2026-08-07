# Architecture — Lab Management System

## Daftar Isi

- [Gambaran Umum](#gambaran-umum)
- [Diagram Arsitektur](#diagram-arsitektur)
- [Layer Arsitektur](#layer-arsitektur)
- [Database Design](#database-design)
- [Service Layer](#service-layer)
- [Realtime & WebSocket](#realtime--websocket)
- [Integrasi Eksternal](#integrasi-eksternal)
- [Keputusan Teknis](#keputusan-teknis)
- [Security Model](#security-model)
- [File Storage](#file-storage)

---

## Gambaran Umum

Lab Management adalah aplikasi monolith Laravel dengan arsitektur berlapis (layered architecture). Sistem dibagi menjadi dua domain utama yang berjalan dalam satu codebase:

1. **Domain Lab** — jadwal, booking, jurnal, tugas, inventaris, kontrol internet
2. **Domain Finance** — keuangan lab dengan autentikasi terpisah

Keduanya berbagi infrastructure (database server, Redis, queue) tetapi menggunakan database PostgreSQL yang berbeda dan guard autentikasi yang berbeda.

---

## Diagram Arsitektur

### Production Stack

```
Internet / Nginx Proxy Manager
        │
        ▼
┌───────────────────────────────────────────────────────────┐
│                    Docker Swarm Cluster                    │
│                                                           │
│  ┌─────────────┐    ┌─────────────┐                       │
│  │  Nginx      │    │  Nginx      │  ← 1 replica          │
│  │  :8080      │    │  conf from  │                       │
│  │             │    │  Swarm cfg  │                       │
│  └──────┬──────┘    └─────────────┘                       │
│         │ fastcgi                                          │
│         ▼                                                 │
│  ┌─────────────┐  ┌─────────────┐                         │
│  │  lab_app    │  │  lab_app    │  ← 2 replicas (workers) │
│  │  PHP-FPM    │  │  PHP-FPM    │                         │
│  │  :9000      │  │  :9000      │                         │
│  └──────┬──────┘  └──────┬──────┘                         │
│         │                │                                │
│  ┌──────▼────────────────▼──────┐                         │
│  │          Shared Network      │                         │
│  └──────┬──────────────┬────────┘                         │
│         │              │                                  │
│  ┌──────▼───┐    ┌──────▼──────┐                          │
│  │ queue    │    │  scheduler  │  ← 1 replica each        │
│  │ Horizon  │    │  cron loop  │                          │
│  └──────────┘    └─────────────┘                          │
│                                                           │
│  ┌───────────┐  ┌───────────┐  ┌───────────┐             │
│  │ reverb    │  │ postgres  │  │  redis    │  ← infra    │
│  │ :8084 WS  │  │ :5432     │  │  :6379    │             │
│  └───────────┘  └───────────┘  └───────────┘             │
└───────────────────────────────────────────────────────────┘

External Services:
  ┌────────────────┐  ┌────────────────┐  ┌──────────────┐
  │  MikroTik Bot  │  │ Baileys WA Bot │  │  Fonnte      │
  │  Python/FastAPI│  │  Node.js       │  │  Webhook     │
  └────────────────┘  └────────────────┘  └──────────────┘
```

### Development Stack

```
Browser
  │
  ├── HTTP  → Nginx :8080 → PHP-FPM (lab_app) :9000
  ├── WS    → Nginx :8080/app → Reverb (lab_reverb) :8084
  └── HMR   → Vite dev server (lab_vite) :5173
```

---

## Layer Arsitektur

### 1. Presentation Layer (Views)

Blade templates dengan pendekatan **partial-based**:

```
resources/views/
├── layouts/
│   ├── app.blade.php          # Admin layout (sidebar + topbar)
│   └── public-schedule.blade.php  # Layout halaman publik (navbar)
├── components/
│   └── app-layout.blade.php   # x-app-layout component
├── booking/
│   ├── index.blade.php        # Compose dari partials
│   └── partials/
│       ├── weekly-table.blade.php
│       ├── weekly-header.blade.php
│       ├── filter-bar.blade.php
│       └── booking-list.blade.php
└── [modul lain]/
```

**Filosofi:** Setiap halaman utama hanya berisi `@include` partial. Partial yang dapat di-swap via AJAX (misal weekly-table booking) di-inject ulang oleh controller sebagai response partial.

### 2. HTTP Layer (Controllers)

Controllers tipis — hanya orchestrate, tidak mengandung business logic:

```php
// Pola controller yang digunakan:
class BookingController extends Controller
{
    public function __construct(
        private BookingQueryService   $query,
        private BookingApprovalService $approval,
        // ...
    ) {}

    public function index(Request $request)
    {
        // Controller hanya: validate → call service → return view
        $data = $this->query->getBookings($request);
        return view('booking.index', compact('data'));
    }
}
```

**Middleware stack:**
- `auth` — Laravel session auth untuk admin
- `CheckRole` — validasi role user (admin/operator/teknisi)
- `CheckLabAccess` — teknisi hanya akses lab yang ditugaskan
- `FinanceAuth` / `FinanceGuest` — guard terpisah untuk modul finance
- `throttle:N,1` — rate limiting di endpoint publik

### 3. Service Layer

Business logic dipisah ke service classes dalam `app/Services/`:

```
app/Services/
├── Booking/
│   ├── BookingQueryService.php      # Query & filter booking
│   ├── BookingApprovalService.php   # Approve/reject logic
│   ├── BookingAccessService.php     # Access control
│   ├── SundayBookingService.php     # Booking hari minggu
│   └── ConflictCheckerService.php   # Cek konflik slot
├── Schedule/
│   ├── ScheduleQueryService.php     # Query jadwal tetap
│   └── (lainnya)
├── Journal/
│   ├── JournalQueryService.php      # Query jurnal
│   └── JournalAvailabilityService.php
├── DashboardService.php             # Agregasi data dashboard
├── InventoryService.php             # Logik inventaris
├── MikroTikService.php              # Integrasi MikroTik via bot
├── WhatsAppService.php              # Notifikasi WA via Baileys
├── RekapService.php                 # Rekap penggunaan lab
└── TransactionService.php          # (Finance) logik transaksi
```

### 4. Data Layer (Models)

Eloquent ORM dengan relasi yang jelas:

```
Core Domain:
  Resource (lab) ──< Schedule ──> Teacher
       │                │
       │           TimeSlot
       │
       ├──< Booking ──> Teacher, TimeSlot, Organization
       ├──< SundayBooking
       ├──< LabJournal ──< LabJournalPhoto
       └──< LabInventory ──< InventoryMaintenanceLog

Auth Domain:
  User >──< Resource (pivot: resource_user)
  User ──> Organization

Assignment Domain:
  Assignment ──< AssignmentSubmission
  LabClass ──> Organization
  Teacher ──< Assignment

Infrastructure Domain:
  MikroTikDevice ──< MikroTikLab ──> Resource
  Setting (key-value store)

Finance Domain (DB terpisah):
  FinanceUser
  Transaction ──> Category, Account, BudgetPeriod
  Budget ──> Category, BudgetPeriod
  WaSetting
  WaNotificationLog
```

---

## Database Design

### Dual Database Strategy

Sistem menggunakan **dua database PostgreSQL terpisah** pada server yang sama:

| Database | Koneksi | Isi |
|----------|---------|-----|
| `lab_management` | `DB_CONNECTION=pgsql` (default) | Semua data lab |
| `finance` | `DB_FINANCE_*` | Data keuangan terisolasi |

Model Finance menggunakan `protected $connection = 'finance'` dan guard autentikasi terpisah (`FinanceUser`, bukan `User`).

**Alasan:** Isolasi data keuangan dari akses lab, memungkinkan permission berbeda, dan memudahkan backup terpisah.

### Tabel Utama

```sql
-- Jadwal tetap
schedules (id, resource_id, time_slot_id, teacher_id, day_of_week, ...)

-- Booking insidental
bookings (id, session_id, resource_id, time_slot_id, teacher_id,
          booking_date, status [pending|approved|rejected], ...)

-- Jurnal penggunaan
lab_journals (id, source_type [schedule|booking], source_id,
              resource_id, journal_date, teacher_name, ...)
lab_journal_photos (id, lab_journal_id, photo_path, sort_order)

-- Tugas siswa
assignments (id, teacher_id, title, deadline, class_name, ...)
assignment_submissions (id, assignment_id, student_name,
                        file_path, file_ext, status, grade, ...)

-- MikroTik
mikrotik_devices (id, name, host, bot_url, bot_token, is_active)
mikrotik_labs (id, mikrotik_device_id, resource_id, lab_key, ...)

-- Auth & Access
users (id, username, role, metadata[json], ...)
resource_user (user_id, resource_id)  -- pivot teknisi ↔ lab
```

### Caching Strategy

| Data | Cache Driver | TTL |
|------|-------------|-----|
| Session user | Redis | 120 menit |
| MikroTik lab map | Redis | 1 jam |
| Class PIN lookup | Redis | 5 menit |
| Active assignments per kelas | Redis | 2 menit |
| Application config | File (production) | Permanen sampai clear |

---

## Service Layer

### Booking Flow

```
User klik "Booking" di jadwal publik
        │
        ▼
ScheduleController::storeBooking()
        │
        ├── ConflictCheckerService::check()   ← cek slot kosong
        ├── BookingAccessService::validate()   ← validasi akses
        │
        ▼
Booking::create() + session_id (UUID)
        │
        ├── Event: BookingCreated::broadcast() → channel 'bookings'
        └── (opsional) WhatsAppService::notifyAdmin()

Admin approve:
BookingController::approve()
        │
        ├── BookingApprovalService::approve()
        └── Event: ScheduleUpdated::broadcast() → channel 'schedules'
```

### Journal Upload Flow

```
Guru submit jurnal + foto
        │
        ▼
JournalController::store()
        │
        ├── Validate (photos: max 5, 5MB each)
        ├── foreach photos → Storage::disk('public')->store()
        │     path: lab_journals/{Y/m/d}/{uuid}/
        │
        ├── DB::transaction()
        │     ├── LabJournal::updateOrCreate()
        │     ├── LabJournalPhoto::delete() (hapus lama + storage)
        │     └── LabJournalPhoto::create() (baru)
        │
        └── catch → Storage::delete() semua file yang sudah upload
```

---

## Realtime & WebSocket

### Channel & Events

```
Channel: 'schedules'
  Event: 'schedule.updated'
  Payload: { type, action, data: { resource_id, booking_date } }
  Trigger: Approve/reject/delete booking

Channel: 'bookings'
  Event: 'booking.created'
  Payload: { id, resource_id, teacher_name, booking_date, status }
  Trigger: Booking baru dibuat (dari halaman publik)
```

### Client-Side Handling

```javascript
// bootstrap.js — setup global Echo
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: VITE_REVERB_APP_KEY,
    wsHost: window.location.hostname,
    // ...
});

// booking.js — subscribe ke updates
window.Echo.channel('schedules')
    .listen('.schedule.updated', (e) => {
        // Reload weekly grid partial via fetch() tanpa reload halaman
        bkLoadWeek(currentWeekDate, false);
    });

// booking.js channel 'bookings' ada di bootstrap.js
// → tampilkan toast notification ke admin
```

### Reverb Configuration

Di production, Reverb berjalan sebagai container terpisah (`lab_reverb`). Nginx mem-proxy WebSocket connection dari `/app` ke Reverb:

```nginx
location /app {
    proxy_pass http://lab_reverb:8082;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_read_timeout 86400;
}
```

---

## Integrasi Eksternal

### MikroTik via Bot API

Sistem tidak langsung berkomunikasi dengan MikroTik. Alih-alih, sistem memanggil **Bot API (Python/FastAPI)** yang berjalan di jaringan internal dan Bot-lah yang mengontrol MikroTik:

```
Lab Management App
        │ HTTP POST /api/lab/internet
        ▼
MikroTik Bot (Python)
        │ RouterOS API
        ▼
MikroTik Router
```

Konfigurasi disimpan di tabel `mikrotik_devices` dan `mikrotik_labs` (database-driven, tidak hardcode di `.env`).

### WhatsApp via Baileys

```
Finance TransactionController
        │
        ▼
WhatsAppService::notifyIncome()
        │ HTTP POST /send atau /send-bulk
        ▼
Baileys Node.js Server
        │ WA Web API
        ▼
WhatsApp
```

Target penerima notifikasi (nomor HP atau grup WA) dikonfigurasi melalui halaman `/finance/wa-settings`.

---

## Keputusan Teknis

### Mengapa Monolith, Bukan Microservice?

- Tim kecil, deployment sederhana lebih diutamakan
- Semua domain saling terkait erat (jadwal → booking → jurnal)
- Docker Swarm sudah cukup untuk scale horizontal
- Pemeliharaan lebih mudah

### Mengapa Partial-Based Views, Bukan SPA?

- Tidak butuh JavaScript framework berat
- SEO dan page load lebih sederhana
- AJAX partial swap (misal weekly booking grid) sudah cukup untuk UX yang baik
- Alpine.js untuk interaktivitas ringan, Livewire untuk komponen stateful

### Mengapa Dual Database?

- Data keuangan sensitif, perlu isolasi akses
- Finance module bisa dikembangkan/deploy terpisah di masa depan
- Backup dan restore bisa dilakukan independen

### Mengapa `public` Disk untuk Jurnal, `local` untuk Tugas?

- Foto jurnal perlu diakses langsung dari browser (URL publik) → `public` disk
- File tugas siswa sensitif, hanya boleh didownload via controller yang sudah terautentikasi → `local` disk

---

## Security Model

### Role Hierarchy

```
super_admin
    └── admin
         └── operator
              └── teknisi   ← hanya akses lab yang ditugaskan
                   └── guru
```

### Access Control Layers

1. **Route middleware** `auth` — semua halaman admin butuh login
2. **Middleware `CheckRole`** — beberapa route butuh role tertentu
3. **Middleware `CheckLabAccess`** — teknisi dibatasi ke lab tertentu via `resource_user` pivot + `metadata.allowed_resources`
4. **Controller-level check** — `BookingAccessService::checkResourceAccess()` digunakan di setiap action booking

### Rate Limiting

| Endpoint | Limit |
|----------|-------|
| `POST /booking` | 10 req/menit |
| `POST /tugas/pin` | 10 req/menit |
| `POST /tugas/{id}/submit` | 10 req/menit |
| `POST /guru/verify-token` | — (throttle default) |
| `GET /jadwal-poll` | 60 req/menit |
| `/fonnte-webhook` | 60 req/menit |

### File Upload Security

| File | Disk | Max Size | Allowed Types |
|------|------|----------|---------------|
| Foto jurnal | `public` | 5 MB/foto, max 5 foto | `image/*` |
| File tugas | `local` | 5 MB | pdf, doc, docx, ppt, pptx, xls, xlsx, zip, rar |

File tugas disimpan di `storage/app/submissions/{org}/{class}/{assignment_id}/` dan hanya bisa diakses via `AssignmentAdminController::downloadSubmission()` yang memverifikasi session guru.

---

## File Storage

### Layout Directory Storage

```
storage/app/
└── submissions/                    ← file tugas (disk: local)
    └── {org_slug}/
        └── {class_name}/
            └── {assignment_id}/
                └── {filename}

storage/app/public/                 ← symlink dari public/storage
└── lab_journals/                   ← foto jurnal (disk: public)
    └── {Y}/
        └── {m}/
            └── {d}/
                └── {uuid}/
                    ├── photo1.jpg
                    └── photo2.png
```

### Upload Limits (Berlapis)

```
Nginx: client_max_body_size 25M
  └── PHP: upload_max_filesize = 20M (prod), 50M (dev)
       └── PHP: post_max_size = 20M (prod), 50M (dev)
            └── Laravel: max:5120 (5MB per file)
```

Laravel yang memblok duluan dengan pesan error yang jelas. Nginx hanya sebagai safety net.
