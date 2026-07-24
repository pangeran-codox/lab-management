<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabJournal extends Model
{
    protected $fillable = [
        'source_type', 'source_id', 'resource_id', 'time_slot_id', 'journal_date',
        'teacher_id', 'teacher_name', 'subject_name', 'class_name',
        'activity', 'notes', 'filled_at',
    ];

    protected $casts = [
        'journal_date' => 'date',
        'filled_at' => 'datetime',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function photos()
    {
        return $this->hasMany(LabJournalPhoto::class)->orderBy('sort_order');
    }
}