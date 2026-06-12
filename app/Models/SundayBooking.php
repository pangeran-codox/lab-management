<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SundayBooking extends Model
{
    protected $table = 'sunday_bookings';

    protected $fillable = [
        'teacher_id',
        'resource_id',
        'organization_id',
        'approved_by',
        'booking_date',
        'teacher_name',
        'teacher_phone',
        'class_name',
        'subject_name',
        'title',
        'description',
        'participant_count',
        'status',
        'approved_at',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'approved_at'  => 'datetime',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ─── SCOPES ───────────────────────────────────────────────────────

    /** Booking aktif (pending atau approved) */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'approved']);
    }
}