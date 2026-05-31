<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportantSchedule extends Model
{
    protected $fillable = [
        'resource_id',
        'title',
        'type',
        'date',
        'is_full_day',
        'start_slot_id',
        'end_slot_id',
        'description',
        'color',
        'created_by',
    ];

    protected $casts = [
        'date'        => 'date',
        'is_full_day' => 'boolean',
    ];

    // ── Labels ──────────────────────────────────────────────
    public static array $typeLabels = [
        'exam'      => 'Ujian',
        'olympiad'  => 'Olimpiade',
        'event'     => 'Event',
        'other'     => 'Lainnya',
    ];

    public function getTypeLabelAttribute(): string
    {
        return self::$typeLabels[$this->type] ?? $this->type;
    }

    // ── Relationships ────────────────────────────────────────
    public function resource(): BelongsTo
    {
        return $this->belongsTo(Resource::class);
    }

    public function startSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'start_slot_id');
    }

    public function endSlot(): BelongsTo
    {
        return $this->belongsTo(TimeSlot::class, 'end_slot_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ───────────────────────────────────────────────

    /** Semua jadwal penting untuk lab tertentu di rentang tanggal */
    public function scopeForResourceOnDate($query, int $resourceId, string $date)
    {
        return $query->where('resource_id', $resourceId)
                     ->where('date', $date);
    }

    /** Cek apakah slot tertentu terblokir */
    public function scopeBlockingSlot($query, int $resourceId, string $date, int $slotId)
    {
        return $query->where('resource_id', $resourceId)
                     ->where('date', $date)
                     ->where(function ($q) use ($slotId) {
                         $q->where('is_full_day', true)
                           ->orWhere(function ($q2) use ($slotId) {
                               $q2->where('start_slot_id', '<=', $slotId)
                                  ->where('end_slot_id', '>=', $slotId);
                           });
                     });
    }
}