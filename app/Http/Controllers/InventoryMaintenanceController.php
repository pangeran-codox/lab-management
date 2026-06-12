<?php

namespace App\Http\Controllers;

use App\Models\InventoryMaintenanceLog;
use App\Models\LabInventory;
use App\Services\Booking\BookingAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryMaintenanceController extends Controller
{
    public function __construct(
        private BookingAccessService $accessService
    ) {}

    public function index(Request $request)
    {
        $allowed = $this->accessService->getAllowedResources();

        $logs = InventoryMaintenanceLog::with(['inventory.resource', 'user'])
            ->when($allowed, function ($query) use ($allowed) {
                return $query->whereHas('inventory', function ($q) use ($allowed) {
                    $q->whereIn('resource_id', $allowed);
                });
            })
            ->orderBy('maintenance_date', 'desc')
            ->paginate(15);

        return view('inventory.maintenance.index', compact('logs'));
    }

    public function store(Request $request)
    {
        $inventory = LabInventory::findOrFail($request->lab_inventory_id);

        if (!$this->accessService->checkResourceAccess($inventory->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses ke lab ini.');
        }

        $validated = $request->validate([
            'lab_inventory_id' => 'required|exists:lab_inventory,id',
            'maintenance_date' => 'required|date',
            'maintenance_type' => 'required|string|max:255',
            'description'      => 'required|string',
            'cost'             => 'required|numeric|min:0',
            'status'           => 'required|string|max:255',
            'fix_quantity'     => 'nullable|integer|min:0', // Jumlah barang yang berhasil diperbaiki
        ]);

        $validated['user_id'] = auth()->id();

        DB::transaction(function () use ($validated, $inventory, $request) {
            InventoryMaintenanceLog::create($validated);

            // Jika ada jumlah yang diperbaiki, update status inventaris secara otomatis
            if ($request->fix_quantity > 0) {
                $fixQty = (int)$request->fix_quantity;
                
                // Pindahkan dari broken ke good
                if ($inventory->quantity_broken >= $fixQty) {
                    $inventory->quantity_broken -= $fixQty;
                    $inventory->quantity_good += $fixQty;
                    $inventory->save();
                }
            }
        });

        return back()->with('success', 'Log perbaikan berhasil dicatat.');
    }

    public function destroy(InventoryMaintenanceLog $log)
    {
        if (!$this->accessService->checkResourceAccess($log->inventory->resource_id)) {
            return back()->with('error', 'Anda tidak memiliki akses.');
        }

        $log->delete();
        return back()->with('success', 'Log perbaikan berhasil dihapus.');
    }
}
