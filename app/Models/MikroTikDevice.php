<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MikroTikDevice extends Model
{
    protected $table = 'mikrotik_devices';

    protected $fillable = [
        'name', 'host', 'port', 'username', 'password',
        'bot_url', 'bot_token', 'is_active', 'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'port'      => 'integer',
    ];

    // Sembunyikan password dari serialisasi JSON (misal API response)
    protected $hidden = ['password', 'bot_token'];

    // ── Relasi ──────────────────────────────────────────────────────────

    public function labs(): HasMany
    {
        return $this->hasMany(MikroTikLab::class, 'mikrotik_device_id');
    }

    public function activeLabs(): HasMany
    {
        return $this->hasMany(MikroTikLab::class, 'mikrotik_device_id')
                    ->where('is_active', true);
    }

    // ── Helpers ─────────────────────────────────────────────────────────

    /**
     * Kembalikan array config format lama LAB_CONFIG agar kompatibel
     * dengan MikroTikService yang menggunakan key => config.
     * [ 'lab7' => [...], 'lab8' => [...] ]
     */
    public function toLabConfigArray(): array
    {
        return $this->labs->keyBy('lab_key')->map(fn($lab) => [
            'name'        => $lab->resource->name ?? $lab->lab_key,
            'nat_comment' => $lab->nat_comment ?? '',
            'interface'   => $lab->interface   ?? '',
            'dhcp_server' => $lab->dhcp_server ?? '',
            'network'     => $lab->network     ?? '',
            'vlan_id'     => $lab->vlan_id,
            'resource_id' => $lab->resource_id,
            'bot_lab_id'  => $lab->bot_lab_id,
            'device_id'   => $this->id,
            'bot_url'     => $this->bot_url,
        ])->all();
    }

    /**
     * Load semua active devices + labs + resources, cached per request.
     * Gunakan ini sebagai pengganti LAB_CONFIG hardcoded.
     */
    public static function getLabMap(): array
    {
        static $cache = null;
        if ($cache !== null) return $cache;

        $cache = [];
        $devices = self::where('is_active', true)
            ->with(['labs' => fn($q) => $q->where('is_active', true)->with('resource')])
            ->get();

        foreach ($devices as $device) {
            foreach ($device->labs as $lab) {
                $cache[$lab->lab_key] = [
                    'name'        => $lab->resource->name ?? $lab->lab_key,
                    'nat_comment' => $lab->nat_comment ?? '',
                    'interface'   => $lab->interface   ?? '',
                    'dhcp_server' => $lab->dhcp_server ?? '',
                    'network'     => $lab->network     ?? '',
                    'vlan_id'     => $lab->vlan_id,
                    'resource_id' => $lab->resource_id,
                    'bot_lab_id'  => $lab->bot_lab_id,
                    'device_id'   => $device->id,
                    'bot_url'     => $device->bot_url,
                    'bot_token'   => $device->bot_token,
                ];
            }
        }

        return $cache;
    }

    /** Invalidate static cache (dipanggil setelah save/delete) */
    public static function clearLabMapCache(): void
    {
        // Reset static cache
        $ref = new \ReflectionFunction(function() {});
        // Cara sederhana: set ulang via closure trick tidak bisa,
        // gunakan Laravel Cache sebagai fallback
        \Illuminate\Support\Facades\Cache::forget('mikrotik_lab_map');
    }

    /**
     * Versi getLabMap yang pakai Laravel Cache (ttl 1 jam).
     * Dipakai di production agar tidak query DB setiap request.
     */
    public static function getLabMapCached(): array
    {
        return \Illuminate\Support\Facades\Cache::remember('mikrotik_lab_map', 3600, function () {
            return self::getLabMap();
        });
    }
}
