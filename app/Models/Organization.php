<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use SoftDeletes;

    protected $table = 'organizations';

    public function getSlugAttribute()
    {
        return strtolower(str_replace([' ', '.', ',', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '=', '[', ']', '{', '}', ';', ':', "'", '"', ',', '<', '>', '?', '/', '\\', '|', '`', '~'], '_', $this->name));
    }

    protected $fillable = [
        'name', 'type', 'parent_id', 'address', 'phone', 'email', 'is_active',
    ];

    public function classes()
    {
        return $this->hasMany(LabClass::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}