<?php

namespace App\Services\Booking;

use App\Models\Resource;
use Illuminate\Support\Facades\Auth;

class BookingAccessService
{
    /**
     * Null = admin/operator (akses semua)
     * Array = daftar resource_id yang boleh diakses
     */
    public function getAllowedResources(): ?array
    {
        $user = Auth::user();

        if (!$user) {
            return []; // Jika tidak login, tidak punya akses ke lab manapun
        }

        if ($user->hasFullAccess()) {
            return null;
        }

        return $user->getAssignedLabIds();
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