<?php

namespace App\Http\Controllers;

use App\Services\RekapService;
use Illuminate\Http\Request;

class RekapPublicController extends Controller
{
    public function __construct(
        private RekapService $rekapService
    ) {}

    public function index(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year',  now()->year);

        $data = $this->rekapService->getMonthlyRekap($month, $year);

        $months = [
            1  => 'Januari',  2 => 'Februari', 3  => 'Maret',    4  => 'April',
            5  => 'Mei',      6 => 'Juni',      7  => 'Juli',     8  => 'Agustus',
            9  => 'September',10 => 'Oktober',  11 => 'November', 12 => 'Desember',
        ];
        $years = range(now()->year - 1, now()->year + 1);

        return view('rekap.public', array_merge($data, [
            'month'  => $month,
            'year'   => $year,
            'months' => $months,
            'years'  => $years,
        ]));
    }

    public function exportPdf(Request $request)
    {
        $month = (int) $request->get('month', now()->month);
        $year  = (int) $request->get('year',  now()->year);
        $resourceId = $request->get('resource_id');

        $data = $this->rekapService->getMonthlyRekap($month, $year);

        // Filter lab data if resource_id is provided
        $labData = $data['labData'];
        if ($resourceId) {
            $labData = collect($labData)->filter(fn($lab) => $lab['resource']->id == $resourceId)->values()->all();
        }

        $labName = 'Semua Laboratorium';
        if ($resourceId) {
            $lab = collect($data['labData'])->first(fn($lab) => $lab['resource']->id == $resourceId);
            $labName = $lab ? $lab['resource']->name : $labName;
        }

        $months = [
            1  => 'Januari',  2 => 'Februari', 3  => 'Maret',    4  => 'April',
            5  => 'Mei',      6 => 'Juni',      7  => 'Juli',     8  => 'Agustus',
            9  => 'September',10 => 'Oktober',  11 => 'November', 12 => 'Desember',
        ];

        return view('rekap.reports.editor', [
            'labData'         => $labData,
            'summary'         => $data['summary'],
            'monthName'       => $months[$month],
            'year'            => $year,
            'labName'         => $labName,
            'totalSlotPerDay' => $data['totalSlotPerDay'],
            'lembagaUsage'    => $data['lembagaUsage'],
            'logo'            => \App\Models\Setting::get(\App\Models\Setting::SITE_LOGO),
            'siteName'        => \App\Models\Setting::get(\App\Models\Setting::SITE_NAME, config('app.name')),
            'siteAddress'     => \App\Models\Setting::get(\App\Models\Setting::SITE_ADDRESS, ''),
            'sitePhone'       => \App\Models\Setting::get(\App\Models\Setting::SITE_PHONE, ''),
            'siteHeadName'    => \App\Models\Setting::get(\App\Models\Setting::SITE_HEAD_NAME, ''),
            'reportFooter'    => \App\Models\Setting::get(\App\Models\Setting::REPORT_FOOTER, ''),
            'kopNameSize'     => (int) \App\Models\Setting::get(\App\Models\Setting::KOP_NAME_SIZE, 20),
            'kopAddressSize'  => (int) \App\Models\Setting::get(\App\Models\Setting::KOP_ADDRESS_SIZE, 13),
            'kopPhoneSize'    => (int) \App\Models\Setting::get(\App\Models\Setting::KOP_PHONE_SIZE, 12),
        ]);
    }
}
