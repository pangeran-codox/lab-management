<?php

namespace App\Services;

use App\Models\LabInventory;
use Illuminate\Http\Request;

class InventoryService
{
    /**
     * Kembalikan semua item sebagai Collection.
     * Inventaris admin menggunakan tab per-lab sehingga semua item
     * harus tersedia di client sekaligus — pagination tidak kompatibel
     * dengan sistem tab.
     *
     * Untuk mencegah overload, limit ke 500 item (lebih dari cukup
     * untuk inventaris lab).
     */
    public function getFilteredItems(Request $request, ?array $allowedResources): \Illuminate\Support\Collection
    {
        $query = LabInventory::with('resource')->whereNull('deleted_at');

        if ($allowedResources !== null) {
            $query->whereIn('resource_id', $allowedResources);
        }

        if ($request->filled('resource_id')) {
            $query->where('resource_id', $request->resource_id);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('condition')) {
            $query->where('condition', $request->condition);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('item_name',       'like', '%' . $request->search . '%')
                  ->orWhere('brand',         'like', '%' . $request->search . '%')
                  ->orWhere('model',         'like', '%' . $request->search . '%')
                  ->orWhere('specifications','like', '%' . $request->search . '%');
            });
        }

        return $query->orderBy('resource_id')
                     ->orderBy('category')
                     ->orderBy('item_name')
                     ->limit(500)
                     ->get();
    }

    public function calculateStats(?array $allowedResources): array
    {
        $query = LabInventory::whereNull('deleted_at');

        if ($allowedResources !== null) {
            $query->whereIn('resource_id', $allowedResources);
        }

        $results = $query->selectRaw('
            COUNT(*) as total_items,
            SUM(quantity) as total_units,
            SUM(quantity_good) as total_good,
            SUM(quantity_broken) as total_broken
        ')->first();

        return [
            'total_items'  => (int) ($results->total_items  ?? 0),
            'total_units'  => (int) ($results->total_units  ?? 0),
            'total_good'   => (int) ($results->total_good   ?? 0),
            'total_broken' => (int) ($results->total_broken ?? 0),
        ];
    }

    public function getCategories(): array
    {
        return [
            'computer'   => '🖥 Komputer',
            'peripheral' => '⌨ Peripheral',
            'furniture'  => '🪑 Furnitur',
            'network'    => '🌐 Jaringan',
            'software'   => '💿 Software',
            'other'      => '📦 Lainnya',
        ];
    }

    public function getConditions(): array
    {
        return [
            'excellent' => 'Sangat Baik',
            'good'      => 'Baik',
            'fair'      => 'Cukup',
            'poor'      => 'Buruk',
            'broken'    => 'Rusak',
        ];
    }
}
