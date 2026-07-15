<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use SoftDeletes;

    protected $table = 'schedules';

    protected $fillable = [
        'resource_id', 'time_slot_id', 'class_id', 'user_id',
        'day_of_week', 'teacher_name', 'subject_name',
        'notes', 'academic_year', 'semester',
        'start_date', 'end_date', 'status',
    ];

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }

    public function timeSlot()
    {
        return $this->belongsTo(TimeSlot::class);
    }

    public function labClass()
    {
        return $this->belongsTo(LabClass::class, 'class_id');
    }

    public function absences()
    {
        return $this->hasMany(ScheduleAbsence::class);
    }
}