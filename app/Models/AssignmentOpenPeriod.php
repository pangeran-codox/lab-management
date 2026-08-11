<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentOpenPeriod extends Model
{
    protected $fillable = [
        'assignment_id', 'round_number', 'opened_at',
        'deadline', 'allow_resubmit', 'closed_at',
    ];

    protected $casts = [
        'opened_at'      => 'datetime',
        'deadline'       => 'datetime',
        'closed_at'      => 'datetime',
        'allow_resubmit' => 'boolean',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class, 'open_period_id');
    }

    public function isOpen(): bool
    {
        return is_null($this->closed_at);
    }
}