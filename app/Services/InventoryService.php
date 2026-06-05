<?php

namespace App\Services;

use App\Models\LabInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class InventoryService
{
    public function getFilteredItems(Request $request, ?array $allowedResources): Collection
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
            $query->where(function($q) use ($request) {
                $q->where('item_name', 'like', '%'.$request->search.'%')
                  ->orWhere('brand', 'like', '%'.$request->search.'%')
                  ->orWhere('model', 'like', '%'.$request->search.'%')
                  ->orWhere('specifications', 'like', '%'.$request->search.'%');
            });
        }

        return $query->orderBy('resource_id')
                     ->orderBy('category')
                     ->orderBy('item_name')
                     ->get();
    }

    public function calculateStats(?array $allowedResources): array
    {
        $statsQuery = LabInventory::whereNull('deleted_at');

        if ($allowedResources !== null) {
            $statsQuery->whereIn('resource_id', $allowedResources);
        }

        return [
            'total_items'  => (clone $statsQuery)->count(),
            'total_units'  => (clone $statsQuery)->sum('quantity'),
            'total_good'   => (clone $statsQuery)->sum('quantity_good'),
            'total_broken' => (clone $statsQuery)->sum('quantity_broken'),
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
