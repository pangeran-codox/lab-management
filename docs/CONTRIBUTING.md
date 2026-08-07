# Contributing Guide — Lab Management System

Panduan untuk developer yang ingin berkontribusi atau melanjutkan pengembangan sistem ini.

---

## Daftar Isi

- [Setup Development](#setup-development)
- [Workflow Git](#workflow-git)
- [Coding Standards](#coding-standards)
- [Struktur Controller & Service](#struktur-controller--service)
- [Menambah Fitur Baru](#menambah-fitur-baru)
- [Membuat Migration](#membuat-migration)
- [Frontend Development](#frontend-development)
- [Testing](#testing)
- [Debugging](#debugging)

---

## Setup Development

### 1. Clone & Setup Environment

```bash
git clone https://github.com/your-org/lab-management.git
cd lab-management

# Salin env
cp .env.example .env

# Edit konfigurasi database, Redis, Reverb
# Minimal yang harus diisi:
# - DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD
# - REDIS_HOST
# - APP_KEY (generate setelah container up)
```

### 2. Buat secret file

```bash
mkdir -p secrets
echo "any-dev-key-here" > secrets/vite_reverb_app_key.txt
```

### 3. Jalankan Docker

```bash
docker compose up -d

# Tunggu container siap (biasanya 15-30 detik)
docker logs lab_vite --tail 20  # tunggu sampai "VITE ready"
```

### 4. Setup aplikasi

```bash
# Generate app key
docker exec lab_app php artisan key:generate

# Jalankan migrasi
docker exec lab_app php artisan migrate

# Seed data (opsional, untuk data dummy)
docker exec lab_app php artisan db:seed

# Buat symlink storage
docker exec lab_app php artisan storage:link
```

Buka `http://lab-rebuild` (atau `http://localhost:8080`).

### 5. Setup Vite HMR

Pastikan `.env` punya:
```env
VITE_HMR_HOST=lab-rebuild
```
Dan file `vite.config.js` sudah ada `watch: { usePolling: true, ignored: [...] }` (sudah ada di repo).

---

## Workflow Git

### Branch Strategy

```
main          ← production-ready, deploy dari sini
  └── develop ← staging/development integration
        ├── feature/nama-fitur    ← fitur baru
        ├── fix/deskripsi-bug     ← bug fix
        └── refactor/nama-bagian  ← refactor kode
```

### Commit Message Convention

Format: `type(scope): deskripsi singkat`

```bash
# Contoh:
git commit -m "feat(booking): tambah AJAX navigation weekly grid"
git commit -m "fix(journal): foto tidak terhapus dari storage saat update"
git commit -m "refactor(booking): pecah index.blade.php ke partials"
git commit -m "chore(docker): tambah client_max_body_size di nginx config"
git commit -m "docs: update DEPLOYMENT.md dengan panduan NPM"
```

**Types:**
- `feat` — fitur baru
- `fix` — bug fix
- `refactor` — perubahan kode tanpa fitur baru atau fix bug
- `style` — CSS/styling
- `docs` — dokumentasi
- `chore` — maintenance (config, dependencies, dll)
- `test` — tambah atau ubah test

### Pull Request

1. Buat branch dari `develop`:
   ```bash
   git checkout develop
   git pull
   git checkout -b feature/nama-fitur
   ```

2. Develop, commit, push:
   ```bash
   git add .
   git commit -m "feat(modul): deskripsi"
   git push -u origin feature/nama-fitur
   ```

3. Buat PR ke `develop` di GitHub dengan deskripsi:
   - Apa yang berubah
   - Screenshot kalau ada perubahan UI
   - Apakah ada migration baru

---

## Coding Standards

### PHP / Laravel

**Gunakan Laravel Pint untuk formatting:**
```bash
docker exec lab_app ./vendor/bin/pint
```

**Aturan penting:**

```php
// ✅ Controller tipis — logic di service
class BookingController extends Controller
{
    public function __construct(
        private BookingQueryService $query,
    ) {}

    public function index(Request $request)
    {
        $data = $this->query->getBookings($request);
        return view('booking.index', compact('data'));
    }
}

// ❌ Hindari logic bisnis di controller
public function index(Request $request)
{
    $data = Booking::where('status', 'pending')
        ->join('resources', ...)
        ->get(); // ← pindahkan ini ke service/repository
}
```

```php
// ✅ Type hints dan return types
public function getBookings(Request $request): Collection
{
    // ...
}

// ✅ Gunakan named arguments untuk clarity
$booking = Booking::create([
    'teacher_name' => $request->teacher_name,
    'status'       => 'pending',
    // ...
]);

// ✅ Eager loading untuk hindari N+1
$bookings = Booking::with(['resource', 'timeSlot', 'teacher'])->get();

// ✅ DB transaction untuk operasi multi-step
DB::transaction(function () use ($data) {
    $journal = LabJournal::create($data);
    LabJournalPhoto::create(['lab_journal_id' => $journal->id, ...]);
});
```

### Blade Templates

```blade
{{-- ✅ Gunakan partial untuk bagian yang bisa dipakai ulang --}}
@include('booking.partials.weekly-header')

{{-- ✅ Escaping konsisten --}}
{{ $variable }}          {{-- auto-escape, untuk output biasa --}}
{!! $html !!}            {{-- raw HTML, pakai hati-hati --}}
{{ addslashes($str) }}   {{-- untuk nilai di dalam onclick="..." --}}

{{-- ✅ Komentar Blade yang informatif --}}
{{-- ════════════════════════════════
     SECTION: Weekly Table
════════════════════════════════ --}}
```

### JavaScript

```javascript
// ✅ Selalu expose fungsi yang dipanggil dari HTML ke window
window.bkOpenAdd = bkOpenAdd;

// ✅ Guard terhadap elemen null
const el = document.getElementById('my-element');
if (!el) return;

// ✅ Gunakan async/await untuk fetch
async function loadData() {
    try {
        const resp = await fetch(url);
        if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
        const data = await resp.text();
        // ...
    } catch (err) {
        console.error('[module] error:', err);
    }
}

// ✅ Debounce untuk operasi yang bisa fire berkali-kali
let _timer = null;
function debouncedReload() {
    clearTimeout(_timer);
    _timer = setTimeout(() => reload(), 500);
}
```

---

## Struktur Controller & Service

### Menambah Endpoint Baru

**1. Tambah route di `routes/web.php`:**
```php
Route::get('/my-feature', [MyFeatureController::class, 'index'])
    ->name('my-feature.index');
```

**2. Buat controller:**
```php
// app/Http/Controllers/MyFeatureController.php
class MyFeatureController extends Controller
{
    public function __construct(
        private MyFeatureService $service
    ) {}

    public function index(Request $request)
    {
        $data = $this->service->getData($request);
        return view('my-feature.index', compact('data'));
    }
}
```

**3. Buat service jika logic cukup kompleks:**
```php
// app/Services/MyFeatureService.php
class MyFeatureService
{
    public function getData(Request $request): Collection
    {
        // Business logic di sini
        return MyModel::query()
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->get();
    }
}
```

**4. Daftarkan service di `AppServiceProvider` jika butuh binding khusus** (biasanya tidak perlu, Laravel auto-resolve via constructor injection).

---

## Menambah Migration

```bash
# Buat migration baru
docker exec lab_app php artisan make:migration add_field_to_table_name

# Jalankan
docker exec lab_app php artisan migrate

# Rollback kalau ada masalah
docker exec lab_app php artisan migrate:rollback
```

**Template migration:**
```php
public function up(): void
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->string('new_field')->nullable()->after('existing_field');
        $table->index('new_field'); // tambah index kalau akan di-query
    });
}

public function down(): void
{
    Schema::table('bookings', function (Blueprint $table) {
        $table->dropColumn('new_field');
    });
}
```

**Untuk Finance (database terpisah):**
```php
// Tambahkan $connection di migration
protected $connection = 'finance';
```

---

## Frontend Development

### CSS

Setiap halaman punya file CSS sendiri di `resources/css/`:

```
resources/css/
├── app.css          ← global admin styles (Tailwind + komponen)
├── booking.css      ← halaman booking
├── journal.css      ← halaman jurnal
├── assignment.css   ← halaman tugas
└── [modul].css      ← buat per-modul
```

Daftarkan di `vite.config.js` jika file baru:
```javascript
input: [
    // ...existing
    'resources/css/my-module.css',
    'resources/js/my-module.js',
],
```

Load di blade dengan:
```blade
@section('vite')
@vite(['resources/css/my-module.css', 'resources/js/my-module.js'])
@endsection
```

Atau untuk halaman admin (x-app-layout):
```blade
@push('styles')
@vite('resources/css/my-module.css')
@endpush
```

### JavaScript

Setiap JS module harus:
1. Expose fungsi yang dipanggil dari HTML ke `window`
2. Tidak bergantung pada global scope polusi
3. Gunakan `const $id = id => document.getElementById(id)` pattern

### Warna & Design Tokens

Sistem menggunakan palet warna konsisten:

```css
:root {
    --g9:    #003d24;  /* hijau sangat gelap */
    --g8:    #00693E;  /* Dartmouth green */
    --g7:    #00874f;  /* hijau sedang */
    --acc:   #B9D9EB;  /* Columbia blue (aksen) */
    --acc2:  #8ec8e0;  /* biru muda */
    --bg:    #f0f7fb;  /* background utama */
    --border:#cce4f0;  /* border */
    --text:  #0d2416;  /* teks utama */
    --muted: #6b8fa3;  /* teks secondary */
}
```

---

## Testing

```bash
# Jalankan semua test
docker exec lab_app php artisan test

# Jalankan test tertentu
docker exec lab_app php artisan test --filter=BookingTest

# Test dengan coverage (butuh Xdebug)
docker exec lab_app php artisan test --coverage
```

> Saat ini test coverage masih minimal. Prioritas test ada di:
> - Service layer (BookingApprovalService, ConflictCheckerService)
> - Controller endpoint kritis (booking store, journal store)

---

## Debugging

### Laravel Telescope

Akses di `http://localhost:8080/telescope` (development only).

Pantau: requests, queries, jobs, exceptions, logs, events.

### Laravel Debugbar

Muncul otomatis di development (DEBUGBAR_ENABLED=true). Tampilkan info queries, memory, timeline di bottom bar.

### Tinker

```bash
docker exec -it lab_app php artisan tinker

# Contoh debugging:
>>> App\Models\Booking::where('status', 'pending')->count()
>>> App\Services\MikroTikService::class  
>>> app(App\Services\BookingQueryService::class)->getBookings(request())
```

### Log

```bash
# Tail log real-time
docker exec lab_app tail -f storage/logs/laravel.log

# Atau dari host:
docker logs lab_app -f
```

### Queue / Horizon

```bash
# Akses Horizon dashboard
open http://localhost:8080/horizon

# Manual debug job
docker exec lab_app php artisan queue:work --once --verbose
```
