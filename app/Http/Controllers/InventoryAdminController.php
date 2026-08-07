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
     * Halaman barang rusak — tampilkan semua item yang punya quantity_broken > 0,
     * dikelompokkan per lab, dengan statistik dan aksi cepat perbaikan.
     */
    public function brokenItems(Request $request)
    {
        $allowed   = $this->accessService->getAllowedResources();
        $resources = $this->accessService->getAccessibleResources();

        $query = LabInventory::with(['resource', 'maintenanceLogs' => function ($q) {
                $q->orderByDesc('maintenance_date')->limit(3);
            }])
            ->whereNull('deleted_at')
            ->where('quantity_broken', '>', 0);

        if ($allowed !== null) {
            $query->whereIn('resource_id', $allowed);
        }

        if ($request->filled('resource_id')) {
            $query->where('resource_id', $request->resource_id);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('item_name',      'like', "%{$search}%")
                  ->orWhere('brand',        'like', "%{$search}%")
                  ->orWhere('model',        'like', "%{$search}%")
                  ->orWhere('specifications','like', "%{$search}%");
            });
        }

        $brokenItems = $query
            ->orderByDesc('quantity_broken')
            ->orderBy('resource_id')
            ->orderBy('item_name')
            ->get();

        // Statistik ringkasan
        $totalBrokenUnits = $brokenItems->sum('quantity_broken');
        $totalItems       = $brokenItems->count();
        $byLab            = $brokenItems->groupBy('resource_id')->map(fn ($g) => [
            'name'    => $g->first()->resource->name ?? '-',
            'count'   => $g->count(),
            'units'   => $g->sum('quantity_broken'),
        ]);

        $categories = $this->inventoryService->getCategories();

        return view('inventory.broken', compact(
            'brokenItems', 'resources', 'byLab',
            'totalBrokenUnits', 'totalItems', 'categories'
        ));
    }

    /**
     * Proses perbaikan barang dari halaman rusak — update quantity_broken & quantity_good,
     * dan catat ke maintenance log secara otomatis.
     */
    public function markFixed(Request $request, LabInventory $inventory)
    {
        if (!$this->accessService->checkResourceAccess($inventory->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $validated = $request->validate([
            'fix_quantity'     => 'required|integer|min:1',
            'maintenance_type' => 'required|string|max:100',
            'description'      => 'required|string|max:500',
            'cost'             => 'nullable|numeric|min:0',
        ], [
            'fix_quantity.required' => 'Jumlah unit yang diperbaiki wajib diisi.',
            'fix_quantity.min'      => 'Minimal 1 unit.',
            'description.required'  => 'Deskripsi perbaikan wajib diisi.',
        ]);

        $fixQty = (int) $validated['fix_quantity'];

        if ($fixQty > $inventory->quantity_broken) {
            return back()->withErrors([
                'fix_quantity' => "Jumlah melebihi unit rusak ({$inventory->quantity_broken} unit).",
            ])->withInput();
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($inventory, $fixQty, $validated) {
            // Pindahkan rusak → baik
            $inventory->quantity_broken -= $fixQty;
            $inventory->quantity_good   += $fixQty;

            // Auto-update kondisi
            $total = $inventory->quantity;
            $good  = $inventory->quantity_good;
            $ratio = $total > 0 ? $good / $total : 0;
            $inventory->condition = match (true) {
                $inventory->quantity_broken === 0 => 'excellent',
                $ratio >= 0.9  => 'good',
                $ratio >= 0.7  => 'fair',
                $ratio >= 0.4  => 'poor',
                default        => 'broken',
            };
            $inventory->updated_by = auth()->id();
            $inventory->save();

            // Catat di maintenance log
            \App\Models\InventoryMaintenanceLog::create([
                'lab_inventory_id' => $inventory->id,
                'user_id'          => auth()->id(),
                'maintenance_date' => now()->toDateString(),
                'maintenance_type' => $validated['maintenance_type'],
                'description'      => $validated['description'],
                'cost'             => $validated['cost'] ?? 0,
                'status'           => 'Selesai',
                'fix_quantity'     => $fixQty,
            ]);
        });

        $remaining = $inventory->fresh()->quantity_broken;
        $msg = "{$fixQty} unit \"{$inventory->item_name}\" berhasil diperbaiki.";
        if ($remaining > 0) {
            $msg .= " Sisa {$remaining} unit masih rusak.";
        }

        return back()->with('success', $msg);
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