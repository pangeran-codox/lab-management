<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LabInventory;
use App\Services\InventoryService;
use App\Services\Booking\BookingAccessService;

class InventoryAdminController extends Controller
{
    public function __construct(
        private InventoryService     $inventoryService,
        private BookingAccessService $accessService
    ) {}

    public function index(Request $request)
    {
        $allowed = $this->accessService->getAllowedResources();

        $items      = $this->inventoryService->getFilteredItems($request, $allowed);
        $stats      = $this->inventoryService->calculateStats($allowed);
        $resources  = $this->accessService->getAccessibleResources();
        $categories = $this->inventoryService->getCategories();
        $conditions = $this->inventoryService->getConditions();

        return view('inventory.admin', compact(
            'items', 'resources', 'stats', 'categories', 'conditions'
        ));
    }

    public function store(Request $request)
    {
        if (!$this->accessService->checkResourceAccess((int) $request->resource_id)) {
            return back()->withErrors(['error' => 'Anda tidak memiliki akses ke lab ini.'])->withInput();
        }

        $validated = $request->validate([
            'resource_id'      => 'required|exists:resources,id',
            'item_name'        => 'required|string|max:255',
            'category'         => 'required|in:computer,peripheral,furniture,network,software,other',
            'brand'            => 'nullable|string|max:100',
            'model'            => 'nullable|string|max:100',
            'serial_number'    => 'nullable|string|max:100',
            'specifications'   => 'nullable|string',
            'condition'        => 'required|in:excellent,good,fair,poor,broken',
            'quantity'         => 'required|integer|min:1',
            'quantity_good'    => 'required|integer|min:0',
            'quantity_broken'  => 'required|integer|min:0',
            'quantity_backup'  => 'required|integer|min:0',
            'notes'            => 'nullable|string',
        ]);

        $validated['status']     = 'active';
        $validated['created_by'] = auth()->id();

        LabInventory::create($validated);

        return back()->with('success', 'Barang "'.$request->item_name.'" berhasil ditambahkan.');
    }

    public function update(Request $request, LabInventory $inventory)
    {
        if (!$this->accessService->checkResourceAccess($inventory->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $validated = $request->validate([
            'item_name'       => 'required|string|max:255',
            'category'        => 'required|in:computer,peripheral,furniture,network,software,other',
            'brand'           => 'nullable|string|max:100',
            'model'           => 'nullable|string|max:100',
            'serial_number'   => 'nullable|string|max:100',
            'specifications'  => 'nullable|string',
            'condition'       => 'required|in:excellent,good,fair,poor,broken',
            'quantity'        => 'required|integer|min:1',
            'quantity_good'   => 'required|integer|min:0',
            'quantity_broken' => 'required|integer|min:0',
            'quantity_backup' => 'required|integer|min:0',
            'notes'           => 'nullable|string',
        ]);

        $validated['updated_by'] = auth()->id();

        $inventory->update($validated);

        return back()->with('success', 'Barang "'.$inventory->item_name.'" berhasil diperbarui.');
    }

    public function destroy(LabInventory $inventory)
    {
        if (!$this->accessService->checkResourceAccess($inventory->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $name = $inventory->item_name;
        $inventory->update(['deleted_at' => now()]);
        return back()->with('success', 'Barang "'.$name.'" berhasil dihapus.');
    }
}
