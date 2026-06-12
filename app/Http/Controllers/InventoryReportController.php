<?php

namespace App\Http\Controllers;

use App\Models\LabInventory;
use App\Models\Resource;
use App\Services\Booking\BookingAccessService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InventoryReportController extends Controller
{
    public function __construct(
        private BookingAccessService $accessService
    ) {}

    public function exportPdf(Request $request)
    {
        $allowed = $this->accessService->getAllowedResources();
        $resourceId = $request->resource_id;

        $items = LabInventory::with('resource')
            ->whereNull('deleted_at')
            ->when($resourceId, fn($q) => $q->where('resource_id', $resourceId))
            ->when($allowed, fn($q) => $q->whereIn('resource_id', $allowed))
            ->orderBy('item_name')
            ->get();

        $labName = 'Semua Laboratorium';
        if ($resourceId) {
            $lab = Resource::find($resourceId);
            $labName = $lab ? $lab->name : $labName;
        }

        return view('inventory.reports.editor', [
            'items' => $items,
            'labName' => $labName,
            'date' => now()->translatedFormat('d F Y'),
        ]);
    }

    // Dihapus karena sudah menggunakan SheetJS di frontend agar menghasilkan file .xlsx asli
    /*
    public function exportExcel(Request $request)
    {
        ...
    }
    */
}
