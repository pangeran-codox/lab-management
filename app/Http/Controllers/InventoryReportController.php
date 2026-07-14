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

        $logo = \App\Models\Setting::get('site_logo');

        return view('inventory.reports.editor', [
            'items'         => $items,
            'labName'       => $labName,
            'date'          => now()->translatedFormat('d F Y'),
            'logo'          => $logo,
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

    // Dihapus karena sudah menggunakan SheetJS di frontend agar menghasilkan file .xlsx asli
    /*
    public function exportExcel(Request $request)
    {
        ...
    }
    */
}
