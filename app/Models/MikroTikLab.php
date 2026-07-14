<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class MikroTikLab extends Model
{
    protected $table = 'mikrotik_labs';

    protected $fillable = [
        'mikrotik_device_id', 'resource_id', 'lab_key', 'bot_lab_id',
        'nat_comment', 'interface', 'dhcp_server', 'network', 'vlan_id',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'bot_lab_id' => 'integer',
        'vlan_id'    => 'integer',
        'resource_id'=> 'integer',
    ];

    // ── Relasi ──────────────────────────────────────────────────────────

    public function device(): BelongsTo
    {
        return $this->belongsTo(MikroTikDevice::class, 'mikrotik_device_id');
    }

    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    /**
     * Teknisi yang di-assign ke lab ini (via resource_user pivot).
     * Filter hanya user dengan role=teknisi.
     */
    public function teknisi()
    {
        return $this->resource
            ? $this->resource->users()->where('role', 'teknisi')
            : collect();
    }

    /**
     * Shortcut: ambil teknisi yang di-assign ke lab ini.
     */
    public function getTeknisiAttribute()
    {
        if (!$this->resource_id) return collect();
        return User::whereHas('resources', fn($q) => $q->where('resource_id', $this->resource_id))
            ->where('role', 'teknisi')
            ->get();
    }

    // ── Boot: invalidate cache saat ada perubahan ────────────────────────

    protected static function booted(): void
    {
        $bust = fn() => MikroTikDevice::clearLabMapCache();
        static::saved($bust);
        static::deleted($bust);
    }
}
