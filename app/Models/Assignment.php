<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'teacher_id', 'organization_id', 'title', 'description',
        'subject_name', 'class_name', 'deadline', 'is_active',
        'allow_student_download',
        'attachment_path', 'attachment_name', 'attachment_size',
        'series_id', 'session_number',
    ];

    protected $casts = [
        'deadline'               => 'datetime',
        'is_active'              => 'boolean',
        'allow_student_download' => 'boolean',
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function openPeriods()
    {
        return $this->hasMany(AssignmentOpenPeriod::class);
    }

    public function isExpired(): bool
    {
        return $this->deadline->isPast();
    }

    /**
     * Period yang sedang aktif (belum ditutup). Null kalau belum pernah
     * dibuka lewat mekanisme reopen (mis. tugas lama sebelum fitur ini ada,
     * atau tugas biasa yang belum pernah di-reopen).
     */
    public function currentOpenPeriod(): ?AssignmentOpenPeriod
    {
        return $this->openPeriods()->whereNull('closed_at')->latest('opened_at')->first();
    }

    /**
     * Semua tugas dalam satu rangkaian materi (series) yang sama,
     * diurutkan dari pertemuan pertama.
     */
    public function seriesSiblings()
    {
        if (!$this->series_id) {
            return collect();
        }

        return static::where('series_id', $this->series_id)
            ->orderBy('session_number')
            ->get(['id', 'title', 'session_number', 'is_active', 'deadline']);
    }

    /**
     * Tugas pertemuan sebelumnya dalam series yang sama (kalau ada).
     */
    public function previousSession(): ?self
    {
        if (!$this->series_id || $this->session_number <= 1) {
            return null;
        }

        return static::where('series_id', $this->series_id)
            ->where('session_number', $this->session_number - 1)
            ->first();
    }

    public function isPartOfSeries(): bool
    {
        return !is_null($this->series_id);
    }
}