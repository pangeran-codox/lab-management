<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use App\Models\Resource;
use App\Models\LabInventory;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InventoryPublicController extends Controller
{
    public function index()
    {
        // Cache resources 5 menit — jarang berubah
        $resources = Cache::remember('active_resources', 300, function () {
            return Resource::where('status', 'active')
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name', 'building', 'capacity', 'status']);
        });

        // Cache inventories 5 menit — jarang berubah real-time
        $inventories = Cache::remember('active_inventories', 300, function () {
            return LabInventory::whereNull('deleted_at')
                ->where('status', 'active')
                ->orderBy('category')
                ->orderBy('item_name')
                ->get([
                    'id', 'resource_id', 'item_name', 'category',
                    'brand', 'model', 'specifications', 'condition',
                    'quantity', 'quantity_good', 'quantity_broken',
                    'quantity_backup', 'notes',
                ]);
        });

        // Hitung stats dari collection — tidak perlu query tambahan
        $totalItems  = $inventories->count();
        $totalUnits  = $inventories->sum('quantity');
        $totalBroken = $inventories->sum('quantity_broken');

        return view('inventory.public', compact(
            'resources', 'inventories',
            'totalItems', 'totalUnits', 'totalBroken'
        ));
    }

    public function exportPdf(Request $request)
    { 
        $resourceId = $request->resource_id;
        
        $items = LabInventory::with('resource')
            ->when($resourceId, fn($q) => $q->where('resource_id', $resourceId))
            ->orderBy('item_name')
            ->get();

        $labName = 'Semua Laboratorium';
        if ($resourceId) {
            $lab = Resource::find($resourceId);
            $labName = $lab ? $lab->name : $labName;
        }

        return view('inventory.reports.editor', [
            'items'         => $items,
            'labName'       => $labName,
            'date'          => now()->translatedFormat('d F Y'),
            'logo'          => \App\Models\Setting::get(\App\Models\Setting::SITE_LOGO),
            'siteName'      => \App\Models\Setting::get(\App\Models\Setting::SITE_NAME, config('app.name')),
            'siteAddress'   => \App\Models\Setting::get(\App\Models\Setting::SITE_ADDRESS, ''),
            'sitePhone'     => \App\Models\Setting::get(\App\Models\Setting::SITE_PHONE, ''),
            'siteHeadName'  => \App\Models\Setting::get(\App\Models\Setting::SITE_HEAD_NAME, ''),
            'reportFooter'  => \App\Models\Setting::get(\App\Models\Setting::REPORT_FOOTER, ''),
            'kopNameSize'    => (int) \App\Models\Setting::get(\App\Models\Setting::KOP_NAME_SIZE, 20),
            'kopAddressSize' => (int) \App\Models\Setting::get(\App\Models\Setting::KOP_ADDRESS_SIZE, 13),
            'kopPhoneSize'   => (int) \App\Models\Setting::get(\App\Models\Setting::KOP_PHONE_SIZE, 12),
        ]);
    }
}