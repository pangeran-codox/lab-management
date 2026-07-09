<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $table = 'users';

    protected $fillable = [
        'username', 'email', 'password_hash',
        'full_name', 'phone', 'role', 'organization_id', 'is_active', 'metadata', 'weekly_quota',
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

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'resource_user');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }

    public function sundayBookings()
    {
        return $this->hasMany(SundayBooking::class, 'user_id');
    }

    public function hasFullAccess(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isTeknisi(): bool
    {
        return $this->role === 'teknisi';
    }

    /**
     * Cek apakah user adalah guru
     */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Hitung kuota terpakai minggu ini (untuk user guru)
     */
    public function getUsedQuotaThisWeek(): int
    {
        if (!$this->isGuru()) {
            return 0;
        }

        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        // Hitung booking biasa dan sunday booking yang status pending/approved
        $bookingsCount = $this->bookings()
            ->whereBetween('booking_date', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        $sundayBookingsCount = $this->sundayBookings()
            ->whereBetween('booking_date', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        return $bookingsCount + $sundayBookingsCount;
    }

    /**
     * Cek apakah masih ada kuota tersisa (untuk user guru)
     */
    public function hasRemainingQuota(int $additionalSlots = 1): bool
    {
        if (!$this->isGuru()) {
            return true; // Bukan guru, tidak ada limit
        }

        $used = $this->getUsedQuotaThisWeek();
        $quota = $this->weekly_quota ?? 5; // Default 5 jika tidak diset

        return ($used + $additionalSlots) <= $quota;
    }

    // Helper: Get metadata resource IDs (cached)
    private function getMetadataResourceIds(): array
    {
        // Use the casted metadata instead of raw attributes
        $metadata = $this->metadata ?? [];
        return $metadata['allowed_resources'] ?? [];
    }

    public function isAssignedToLab(int $labId): bool
    {
        // Check pivot using eager loaded relation if available
        $pivotExists = $this->relationLoaded('resources') 
            ? $this->resources->contains('id', $labId)
            : $this->resources()->where('resource_id', $labId)->exists();
        
        $metadataIds = $this->getMetadataResourceIds();
        
        return $pivotExists || in_array($labId, $metadataIds);
    }

    public function getAssignedLabIds(): array
    {
        // Get pivot IDs from eager loaded relation if available
        $pivotIds = $this->relationLoaded('resources') 
            ? $this->resources->pluck('id')->toArray()
            : $this->resources()->pluck('id')->toArray();
        
        $metadataIds = $this->getMetadataResourceIds();
        
        // Gabungkan kedua sumber, hilangkan duplikat
        return array_values(array_unique(array_merge($pivotIds, $metadataIds)));
    }

    public function getAssignedLabs()
    {
        // Use preloaded assigned_labs if available (from controller)
        if (isset($this->assigned_labs)) {
            return $this->assigned_labs;
        }
        
        $labIds = $this->getAssignedLabIds();
        
        // Use loaded resources relation if available to avoid N+1
        $pivotResources = $this->relationLoaded('resources') ? $this->resources : collect();
        
        $metadataIds = $this->getMetadataResourceIds();
        
        // If no metadata IDs, just use pivot resources
        if (empty($metadataIds)) {
            return $pivotResources->sortBy('name')->values();
        }
        
        // Otherwise, load all required resources in one query
        return Resource::whereIn('id', $labIds)->orderBy('name')->get();
    }
}