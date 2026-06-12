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

    /**
     * Quick update — dipanggil via AJAX dari detail card.
     * Menangani: tambah rusak, gunakan cadangan, barang diperbaiki.
     * Return JSON berisi item terbaru + stats terupdate.
     */
    public function quickUpdate(Request $request, LabInventory $inventory)
    {
        if (!$this->accessService->checkResourceAccess($inventory->resource_id)) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }

        $validated = $request->validate([
            'quantity_good'   => 'required|integer|min:0',
            'quantity_broken' => 'required|integer|min:0',
            'quantity_backup' => 'required|integer|min:0',
            'condition'       => 'nullable|in:excellent,good,fair,poor,broken',
        ]);

        // Validasi: total qty tidak boleh berubah
        $total = $inventory->quantity;
        $sum   = $validated['quantity_good']
               + $validated['quantity_broken']
               + $validated['quantity_backup'];

        if ($sum > $total) {
            return response()->json([
                'success' => false,
                'message' => "Total unit melebihi jumlah barang ($total unit).",
            ], 422);
        }

        // Auto-set condition berdasarkan qty jika tidak dikirim
        if (empty($validated['condition'])) {
            $validated['condition'] = $this->autoCondition(
                $validated['quantity_good'],
                $validated['quantity_broken'],
                $total
            );
        }

        $validated['updated_by'] = auth()->id();
        $inventory->update($validated);
        $inventory->refresh();

        // Hitung ulang stats
        $allowed = $this->accessService->getAllowedResources();
        $stats   = $this->inventoryService->calculateStats($allowed);

        return response()->json([
            'success' => true,
            'message' => 'Data berhasil diperbarui.',
            'item'    => [
                'id'              => $inventory->id,
                'quantity'        => $inventory->quantity,
                'quantity_good'   => $inventory->quantity_good,
                'quantity_broken' => $inventory->quantity_broken,
                'quantity_backup' => $inventory->quantity_backup,
                'condition'       => $inventory->condition,
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Auto-tentukan kondisi berdasarkan rasio baik/total.
     */
    private function autoCondition(int $good, int $broken, int $total): string
    {
        if ($broken === 0)                              return 'excellent';
        $ratio = $total > 0 ? $good / $total : 0;
        if ($ratio >= 0.9)                              return 'good';
        if ($ratio >= 0.7)                              return 'fair';
        if ($ratio >= 0.4)                              return 'poor';
        return 'broken';
    }
}