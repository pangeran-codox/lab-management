<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use SoftDeletes;

    protected $table = 'resources';

    protected $fillable = [
        'name', 'type', 'parent_id', 'organization_id',
        'building', 'floor', 'room_number', 'capacity', 'status', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function inventory()
    {
        return $this->hasMany(LabInventory::class);
    }
    
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'resource_user');
    }
}