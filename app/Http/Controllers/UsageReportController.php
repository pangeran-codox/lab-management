<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Services\Booking\BookingAccessService;
use App\Services\RekapService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UsageReportController extends Controller
{
    public function __construct(
        private BookingAccessService $accessService,
        private RekapService $rekapService
    ) {}

    public function index(Request $request)
    {
        $month = $request->month ? (int)$request->month : now()->month;
        $year = $request->year ? (int)$request->year : now()->year;
        
        // Secara default langsung menampilkan rekap bulan ini
        return $this->showRekap($month, $year);
    }

    private function showRekap(int $month, int $year)
    {
        $allowed = $this->accessService->getAllowedResources();

        // Gunakan RekapService yang sama dengan halaman publik
        $data = $this->rekapService->getMonthlyRekap($month, $year);
        
        // Filter untuk izin akses
        $labData = $data['labData'];
        if ($allowed) {
            $labData = collect($labData)->filter(fn($lab) => in_array($lab['resource']->id, $allowed))->values()->all();
        }

        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();
        
        $years = range(now()->year - 5, now()->year + 1);

        return view('rekap.admin', [
            'labData' => $labData,
            'summary' => $data['summary'],
            'month' => $month,
            'year' => $year,
            'months' => $months,
            'years' => $years,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalSlotPerDay' => $data['totalSlotPerDay'],
            'lembagaUsage' => $data['lembagaUsage'],
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|date_format:Y-m',
            'resource_id' => 'nullable|exists:resources,id',
            'include_sunday' => 'boolean',
        ]);

        $allowed = $this->accessService->getAllowedResources();
        $resourceId = $request->resource_id;

        $monthStart = Carbon::createFromFormat('Y-m', $request->month);
        $month = (int)$monthStart->format('m');
        $year = (int)$monthStart->format('Y');

        // Gunakan RekapService yang sama dengan halaman publik
        $data = $this->rekapService->getMonthlyRekap($month, $year);
        
        // Filter lab jika resource_id diberikan
        $labData = $data['labData'];
        if ($resourceId) {
            $labData = collect($labData)->filter(fn($lab) => $lab['resource']->id == $resourceId)->values()->all();
        }
        
        // Filter untuk izin akses
        if ($allowed) {
            $labData = collect($labData)->filter(fn($lab) => in_array($lab['resource']->id, $allowed))->values()->all();
        }

        return view('rekap.reports.editor', [
            'labData'        => $labData,
            'summary'        => $data['summary'],
            'monthName'      => $this->getMonthName($month),
            'year'           => $year,
            'labName'        => $resourceId ? Resource::find($resourceId)->name : 'Semua Laboratorium',
            'totalSlotPerDay' => $data['totalSlotPerDay'],
            'lembagaUsage'   => $data['lembagaUsage'],
            'logo'           => \App\Models\Setting::get(\App\Models\Setting::SITE_LOGO),
            'siteName'       => \App\Models\Setting::get(\App\Models\Setting::SITE_NAME, config('app.name')),
            'siteAddress'    => \App\Models\Setting::get(\App\Models\Setting::SITE_ADDRESS, ''),
            'sitePhone'      => \App\Models\Setting::get(\App\Models\Setting::SITE_PHONE, ''),
            'siteHeadName'   => \App\Models\Setting::get(\App\Models\Setting::SITE_HEAD_NAME, ''),
            'reportFooter'   => \App\Models\Setting::get(\App\Models\Setting::REPORT_FOOTER, ''),
            'kopNameSize'    => (int) \App\Models\Setting::get(\App\Models\Setting::KOP_NAME_SIZE, 20),
            'kopAddressSize' => (int) \App\Models\Setting::get(\App\Models\Setting::KOP_ADDRESS_SIZE, 13),
            'kopPhoneSize'   => (int) \App\Models\Setting::get(\App\Models\Setting::KOP_PHONE_SIZE, 12),
        ]);
    }
    
    private function getMonthName($month): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        return $months[$month] ?? '';
    }
}
