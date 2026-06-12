<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $table = 'users';

    protected $fillable = [
        'username', 'email', 'password_hash',
        'full_name', 'phone', 'role', 'organization_id', 'is_active', 'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}