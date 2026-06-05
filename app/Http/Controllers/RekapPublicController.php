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
}
