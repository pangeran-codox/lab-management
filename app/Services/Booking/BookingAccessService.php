<?php

namespace App\Services\Booking;

use App\Models\Resource;
use Illuminate\Support\Facades\Auth;

class BookingAccessService
{
    /**
     * Sentinel: PHP_INT_MIN berarti belum pernah di-resolve dalam request ini.
     * Null  = admin/super_admin (akses semua)
     * Array = daftar resource_id yang boleh diakses (teknisi/terbatas)
     */
    private mixed $cachedAllowed = PHP_INT_MIN;

    /**
     * Null = admin/operator (akses semua)
     * Array = daftar resource_id yang boleh diakses
     *
     * Di-cache dalam satu request agar tidak query pivot table
     * berkali-kali (BookingController index() bisa memanggil ini 4x).
     */
    public function getAllowedResources(): ?array
    {
        if ($this->cachedAllowed !== PHP_INT_MIN) {
            return $this->cachedAllowed;
        }

        $user = Auth::user();

        if (!$user) {
            // Tidak login → tidak punya akses ke lab manapun
            return $this->cachedAllowed = [];
        }

        if ($user->hasFullAccess()) {
            return $this->cachedAllowed = null;
        }

        return $this->cachedAllowed = $user->getAssignedLabIds();
    }

    /**
     * Reset cache (berguna saat user assignment berubah dalam satu request,
     * misalnya setelah update user di UserController).
     */
    public function flushCache(): void
    {
        $this->cachedAllowed = PHP_INT_MIN;
    }

    public function checkResourceAccess(int $resourceId): bool
    {
        $allowed = $this->getAllowedResources();

        if ($allowed === null) return true;

        return in_array($resourceId, $allowed);
    }

    public function getAccessibleResources()
    {
        $allowed = $this->getAllowedResources();

        $query = Resource::where('status', 'active')->orderBy('name');

        if ($allowed !== null) {
            $query->whereIn('id', $allowed);
        }

        return $query->get();
    }
}