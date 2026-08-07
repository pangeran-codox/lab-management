# API Reference — Lab Management System

Dokumen ini mencakup semua endpoint yang dapat dikonsumsi oleh pihak eksternal (bot WA, integrasi sistem lain) maupun endpoint internal yang digunakan via AJAX dari frontend.

## Base URL

```
Production: https://lab.nuris.sch.id
Development: http://lab-rebuild (atau http://localhost:8080)
```

## Autentikasi

Sebagian besar endpoint publik tidak butuh autentikasi. Endpoint admin menggunakan session cookie Laravel. Endpoint bot menggunakan `BotAuthMiddleware` dengan token di header.

```
# Bot/internal API
Authorization: Bearer {BOT_TOKEN}
```

---

## Endpoint Publik

### Jadwal

#### `GET /`
Halaman jadwal mingguan publik. Mendukung query parameter untuk navigasi.

**Query Parameters:**
| Parameter | Tipe | Deskripsi |
|-----------|------|-----------|
| `week` | string | Tanggal ISO (YYYY-MM-DD) di dalam minggu yang ingin ditampilkan |
| `resource` | integer | Filter lab tertentu (ID) |

---

#### `GET /jadwal-poll`
Endpoint polling untuk cek perubahan jadwal (fallback dari WebSocket).

**Rate limit:** 60 req/menit

**Response:**
```json
{
  "updated": true,
  "timestamp": "2026-07-24T14:30:00Z"
}
```

---

#### `GET /kelas`
Ambil daftar kelas aktif (dipakai untuk autocomplete form booking).

**Rate limit:** 30 req/menit

**Response:**
```json
[
  { "id": 1, "name": "9A", "organization": "SMKS Nuris Jember" },
  { "id": 2, "name": "9B", "organization": "SMKS Nuris Jember" }
]
```

---

### Booking (Publik)

#### `POST /booking`
Buat booking baru dari halaman jadwal publik (tanpa login).

**Rate limit:** 10 req/menit

**Request Body (form-data atau JSON):**
```json
{
  "resource_id": 3,
  "time_slot_id": 5,
  "booking_date": "2026-07-25",
  "teacher_name": "Bapak Ucup",
  "teacher_phone": "08123456789",
  "class_name": "9A",
  "subject_name": "TIK",
  "title": "Praktikum Jaringan",
  "description": "Materi IPv4",
  "participant_count": 30
}
```

**Response sukses (302 redirect):**  
Redirect ke halaman utama dengan flash message `success`.

**Response error (422):**
```json
{
  "message": "Slot sudah terpakai.",
  "errors": { "time_slot_id": ["Slot ini sudah dibooking."] }
}
```

---

#### `POST /booking-minggu`
Booking lab untuk hari Minggu (full day).

**Rate limit:** 10 req/menit

**Request Body:**
```json
{
  "resource_id": 3,
  "booking_date": "2026-07-27",
  "teacher_name": "Ibu Sari",
  "teacher_phone": "08198765432",
  "class_name": "Ekskul Robotik",
  "title": "Persiapan Lomba",
  "description": "Latihan intensif sebelum kompetisi",
  "participant_count": 15
}
```

---

### Inventaris Publik

#### `GET /inventaris`
Halaman daftar inventaris lab.

#### `GET /inventaris/export/pdf`
Download laporan inventaris dalam format PDF.

---

### Jurnal Lab (Publik)

#### `GET /journal`
Halaman jurnal harian publik.

**Query Parameters:**
| Parameter | Tipe | Deskripsi |
|-----------|------|-----------|
| `date` | string | Tanggal (YYYY-MM-DD), default hari ini |

---

#### `POST /journal`
Submit jurnal beserta foto.

**Content-Type:** `multipart/form-data`

**Request Body:**
```
source_type     = "schedule" atau "booking"
source_ids[]    = [1, 2]             (array ID sumber)
time_slot_ids[] = [3, 4]             (array ID slot)
resource_id     = 5
journal_date    = "2026-07-24"
notes           = "Catatan opsional"
photos[]        = [file1.jpg, file2.jpg]  (max 5, maks 5MB/foto)
```

**Response:** Redirect kembali dengan flash success/error.

---

### Tugas Siswa

#### `GET /tugas`
Halaman daftar tugas (perlu PIN kelas aktif di session).

---

#### `POST /tugas/pin`
Verifikasi PIN kelas.

**Rate limit:** 10 req/menit

**Request Body:**
```json
{ "pin": "123456" }
```

**Response:** Redirect ke `/tugas` jika PIN valid, kembali dengan error jika salah.

---

#### `POST /tugas/ganti-kelas`
Hapus sesi kelas aktif (ganti kelas).

---

#### `GET /tugas/{assignment}`
Halaman detail tugas + form pengumpulan.

---

#### `POST /tugas/{assignment}/submit`
Upload file tugas siswa.

**Rate limit:** 10 req/menit  
**Content-Type:** `multipart/form-data`

**Request Body:**
```
student_name = "Nama Lengkap Siswa"
file         = [file] (pdf/doc/docx/ppt/pptx/xls/xlsx/zip/rar, max 5MB)
```

---

#### `GET /tugas/{assignment}/download-attachment`
Download soal/lampiran dari guru.

---

### Tugas Admin (Token Guru)

#### `GET /tugas-admin`
Halaman admin tugas. Butuh token guru aktif di session atau `?token=` di URL.

---

#### `POST /guru/verify-token`
Verifikasi token guru untuk akses panel tugas.

**Request Body:**
```json
{ "token": "TEACHER_TOKEN_HERE" }
```

---

#### `POST /tugas-admin`
Buat tugas baru (butuh session token guru).

**Content-Type:** `multipart/form-data`

**Request Body:**
```
title            = "Praktikum Jaringan Dasar"
subject_name     = "TIK"
class_name       = "9A"
deadline         = "2026-07-30 23:59"
description      = "Keterangan tugas (opsional)"
attachment       = [file] (opsional, soal untuk diunduh siswa)
```

---

#### `POST /tugas-admin/submission/{submission}/grade`
Beri nilai pada submission siswa.

**Request Body:**
```json
{
  "grade": "90",
  "feedback": "Bagus, tapi diagram perlu diperbaiki"
}
```

---

#### `GET /tugas-admin/submission/{submission}/download`
Download file yang dikumpulkan siswa.

---

### Lab Control (Token)

#### `GET /lab-control/{token}`
Halaman kontrol internet lab. Token unik per lab.

---

#### `GET /lab-control/{token}/status`
Cek status internet lab saat ini.

**Response:**
```json
{
  "success": true,
  "lab_key": "lab7",
  "lab_name": "Lab Komputer 7",
  "nat_enabled": true,
  "status": "online",
  "active_users": 12,
  "devices": [
    { "mac": "AA:BB:CC:DD:EE:FF", "ip": "10.7.0.101", "active": true }
  ]
}
```

---

#### `POST /lab-control/{token}/toggle`
Toggle internet lab (on/off).

**Request Body:**
```json
{ "action": "on" }
```
atau
```json
{ "action": "off" }
```

**Response:**
```json
{
  "success": true,
  "lab": "Lab Komputer 7",
  "action": "enabled",
  "message": "NAT rule enabled"
}
```

---

### Laporan Publik

#### `GET /rekap`
Halaman rekap penggunaan lab publik.

#### `GET /rekap/export/pdf`
Export rekap ke PDF.

---

## Endpoint AJAX Internal (Frontend)

Endpoint ini dipanggil via `fetch()` dari JavaScript browser.

### `GET /booking/weekly-grid`
Ambil partial HTML tabel mingguan booking (dipakai oleh `booking.js` saat navigasi minggu).

**Query Parameters:**
| Parameter | Tipe | Deskripsi |
|-----------|------|-----------|
| `week` | string | Tanggal (YYYY-MM-DD) dalam minggu yang diminta |

**Headers:**
```
X-Requested-With: XMLHttpRequest
```

**Response:** HTML partial `booking.partials.weekly-table`

---

### `GET /api/jadwal-penting/blocked-slots`
Ambil slot yang diblokir oleh jadwal penting (dipakai oleh halaman jadwal publik).

**Query Parameters:**
| Parameter | Tipe | Deskripsi |
|-----------|------|-----------|
| `date` | string | Tanggal (YYYY-MM-DD) |
| `resource_id` | integer | ID lab |

**Response:**
```json
{
  "blocked": [3, 4, 5],
  "events": [
    {
      "title": "Ujian Nasional",
      "color": "#dc2626",
      "is_full_day": true
    }
  ]
}
```

---

### `POST /settings/kop-size`
Simpan preferensi ukuran font kop laporan (dari halaman editor laporan).

**Rate limit:** 10 req/menit

**Request Body:**
```json
{ "size": "12" }
```

---

## Webhook Endpoint

### `POST /fonnte-webhook` (atau `ANY`)
Proxy webhook dari Fonnte ke bot Python.

**Rate limit:** 60 req/menit

Request diteruskan ke `BOT_URL/api/webhook/fonnte`.

---

## Finance API

Semua endpoint finance berada di prefix `/finance/`. Memerlukan session `finance.auth`.

### Dashboard
`GET /finance/` — Dashboard keuangan

### Transactions
```
GET    /finance/transactions          Daftar transaksi
GET    /finance/transactions/create   Form tambah
POST   /finance/transactions          Simpan transaksi baru
GET    /finance/transactions/{id}     Detail transaksi
DELETE /finance/transactions/{id}     Hapus transaksi
```

**POST /finance/transactions Body:**
```json
{
  "type": "income",
  "amount": 500000,
  "description": "Iuran laboratorium bulan Juli",
  "category_id": 2,
  "account_id": 1,
  "date": "2026-07-24",
  "notes": "Diterima dari bendahara kelas"
}
```

### Budgets
```
GET    /finance/budgets               Daftar anggaran
POST   /finance/budgets               Buat anggaran baru
PUT    /finance/budgets/{id}          Update anggaran
DELETE /finance/budgets/{id}          Hapus anggaran
```

### Laporan
`GET /finance/laporan` — Laporan keuangan dengan filter periode

### WA Settings (admin only)
```
GET  /finance/wa-settings     Halaman pengaturan WA
POST /finance/wa-settings     Update konfigurasi
POST /finance/wa-settings/test  Kirim pesan test
GET  /finance/wa-qr           Halaman scan QR Baileys
```

---

## WebSocket Events

### Channel: `schedules`

**Event:** `.schedule.updated`

Broadcast saat booking di-approve/reject/hapus.

```json
{
  "type": "regular",
  "action": "updated",
  "data": {
    "resource_id": 3,
    "booking_date": "2026-07-24"
  }
}
```

**type:** `regular` | `sunday`  
**action:** `created` | `updated` | `deleted`

---

### Channel: `bookings`

**Event:** `.booking.created`

Broadcast saat booking baru dibuat dari halaman publik.

```json
{
  "id": 42,
  "resource_id": 3,
  "title": "Praktikum Jaringan",
  "teacher_name": "Bapak Ucup",
  "resource": "Lab Komputer 7",
  "booking_date": "2026-07-25",
  "status": "pending"
}
```

**Cara subscribe (JavaScript):**
```javascript
window.Echo.channel('schedules')
    .listen('.schedule.updated', (e) => {
        console.log('Jadwal diperbarui:', e);
    });

window.Echo.channel('bookings')
    .listen('.booking.created', (e) => {
        console.log('Booking baru:', e);
        showGlobalNotification(`Booking baru: ${e.title} oleh ${e.teacher_name}`);
    });
```

---

## Error Codes

| HTTP Status | Kondisi |
|-------------|---------|
| `200` | Sukses |
| `302` | Redirect (form POST) |
| `401` | Belum login (admin routes) |
| `403` | Tidak punya akses ke resource ini |
| `404` | Data tidak ditemukan |
| `413` | File terlalu besar (> 25MB, ditolak Nginx) |
| `422` | Validasi gagal (input tidak valid) |
| `429` | Rate limit terlampaui |
| `500` | Server error |

**Format error 422:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": ["Pesan error spesifik"]
  }
}
```
