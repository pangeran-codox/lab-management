<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    // ──────────────────────────────────────────────────────────
    // Core get / set
    // ──────────────────────────────────────────────────────────

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        self::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    /**
     * Save multiple key-value pairs at once.
     * Accepts ['key' => 'value', ...]
     */
    public static function setMany(array $data): void
    {
        foreach ($data as $key => $value) {
            self::set($key, $value);
        }
    }

    /**
     * Get multiple keys at once.
     * Returns ['key' => value, ...]
     */
    public static function getMany(array $keys): array
    {
        $rows = self::whereIn('key', $keys)->pluck('value', 'key');
        $result = [];
        foreach ($keys as $key) {
            $result[$key] = $rows[$key] ?? null;
        }
        return $result;
    }

    // ──────────────────────────────────────────────────────────
    // Boolean helper
    // ──────────────────────────────────────────────────────────

    public static function isEnabled(string $key): bool
    {
        return self::get($key, '1') === '1';
    }

    // ──────────────────────────────────────────────────────────
    // All known setting keys
    // ──────────────────────────────────────────────────────────

    // Identitas Sekolah / Lab
    const SITE_NAME            = 'site_name';
    const SITE_LOGO            = 'site_logo';
    const SITE_HEAD_NAME       = 'site_head_name';   // Kepala Lab / Penanggung Jawab
    const SITE_ADDRESS         = 'site_address';
    const SITE_PHONE           = 'site_phone';

    // Notifikasi WhatsApp
    const WA_NOTIFY_BOOKING    = 'wa_notify_booking';    // 1 / 0
    const WA_NOTIFY_LAB        = 'wa_notify_lab';        // 1 / 0
    const WA_ADMIN_NUMBER      = 'wa_admin_number';      // nomor WA admin (62xxx)

    // Booking
    const BOOKING_OPEN         = 'booking_open';         // 1 / 0
    const BOOKING_MAX_DAYS     = 'booking_max_days';     // integer, default 30

    // Lab Control / Sesi
    const LAB_SESSION_AUTO     = 'lab_session_auto';     // 1 / 0 – auto-generate token dari jadwal
    const LAB_TOKEN_GRACE      = 'lab_token_grace';      // integer menit toleransi setelah sesi berakhir

    // Laporan — footer saja, kop dibentuk otomatis dari identitas
    const REPORT_FOOTER     = 'report_footer'; // catatan bawah laporan

    // Ukuran font kop laporan (px)
    const KOP_NAME_SIZE     = 'kop_name_size';     // default 20
    const KOP_ADDRESS_SIZE  = 'kop_address_size';  // default 13
    const KOP_PHONE_SIZE    = 'kop_phone_size';    // default 12
}
