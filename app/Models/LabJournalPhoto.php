<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabJournalPhoto extends Model
{
    public $timestamps = false;

    protected $fillable = ['lab_journal_id', 'photo_path', 'sort_order'];

    protected $appends = ['url'];

    public function journal()
    {
        return $this->belongsTo(LabJournal::class, 'lab_journal_id');
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->photo_path);
    }
}