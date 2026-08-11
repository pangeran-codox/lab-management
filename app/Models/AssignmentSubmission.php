<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id', 'open_period_id', 'student_name', 'student_class',
        'file_path', 'file_name', 'file_size', 'file_ext',
        'status', 'grade', 'feedback', 'submitted_at'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function openPeriod()
    {
        return $this->belongsTo(AssignmentOpenPeriod::class, 'open_period_id');
    }
}