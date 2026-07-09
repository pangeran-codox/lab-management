<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Transaction;
use App\Models\Finance\BudgetPeriod;
use App\Models\Finance\Budget;
use App\Models\Finance\Category;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->integer('month', now()->month);
        $year  = $request->integer('year', now()->year);

        // Validasi range
        $month = max(1, min(12, $month));

        $monthName = Carbon::create($year, $month, 1)->translatedFormat('F Y');

        // ── Bulan sebelumnya untuk perbandingan ─────────────────
        $prevDate  = Carbon::create($year, $month, 1)->subMonth();
        $prevMonth = $prevDate->month;
        $prevYear  = $prevDate->year;

        // ── Summary bulan ini ───────────────────────────────────
        $totalIncome  = Transaction::income()->byPeriod($month, $year)->whereNull('deleted_at')->sum('amount');
        $totalExpense = Transaction::expense()->byPeriod($month, $year)->whereNull('deleted_at')->sum('amount');
        $netBalance   = $totalIncome - $totalExpense;

        // ── Summary bulan sebelumnya ────────────────────────────
        $prevIncome  = Transaction::income()->byPeriod($prevMonth, $prevYear)->whereNull('deleted_at')->sum('amount');
        $prevExpense = Transaction::expense()->byPeriod($prevMonth, $prevYear)->whereNull('deleted_at')->sum('amount');
        $prevNet     = $prevIncome - $prevExpense;

        // ── Persentase perubahan ────────────────────────────────
        $incomeChange  = $prevIncome  > 0 ? round((($totalIncome  - $prevIncome)  / $prevIncome)  * 100, 1) : null;
        $expenseChange = $prevExpense > 0 ? round((($totalExpense - $prevExpense) / $prevExpense) * 100, 1) : null;

        // ── Breakdown income per kategori ───────────────────────
        $incomeByCategory = Transaction::with('category')
            ->income()
            ->byPeriod($month, $year)
            ->whereNull('deleted_at')
            ->get()
            ->groupBy('category_id')
            ->map(fn($trx) => [
                'name'    => $trx->first()->category?->name ?? 'Uncategorized',
                'color'   => $trx->first()->category?->color ?? '#6b7280',
                'total'   => (float) $trx->sum('amount'),
                'count'   => $trx->count(),
            ])
            ->sortByDesc('total')
            ->values();

        // ── Breakdown expense per kategori ──────────────────────
        $expenseByCategory = Transaction::with('category')
            ->expense()
            ->byPeriod($month, $year)
            ->whereNull('deleted_at')
            ->get()
            ->groupBy('category_id')
            ->map(fn($trx) => [
                'name'    => $trx->first()->category?->name ?? 'Uncategorized',
                'color'   => $trx->first()->category?->color ?? '#6b7280',
                'total'   => (float) $trx->sum('amount'),
                'count'   => $trx->count(),
            ])
            ->sortByDesc('total')
            ->values();

        // ── Semua transaksi bulan ini ───────────────────────────
        $transactions = Transaction::with(['category', 'account'])
            ->byPeriod($month, $year)
            ->whereNull('deleted_at')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->get();

        // ── Anggaran periode ini ────────────────────────────────
        $period  = BudgetPeriod::where('month', $month)->where('year', $year)->first();
        $budgets = $period
            ? Budget::with('category')->where('budget_period_id', $period->id)->get()
            : collect();

        // ── Trend harian (untuk chart garis) ───────────────────
        $dailyData = Transaction::byPeriod($month, $year)
            ->whereNull('deleted_at')
            ->selectRaw("DATE(transaction_date) as date, type, SUM(amount) as total")
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $daysInMonth  = Carbon::create($year, $month)->daysInMonth;
        $dailyIncome  = [];
        $dailyExpense = [];
        $dailyLabels  = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $dateKey = Carbon::create($year, $month, $d)->toDateString();
            $dailyLabels[]  = $d;
            $dayData        = $dailyData->get($dateKey, collect());
            $dailyIncome[]  = (float) ($dayData->where('type', 'income')->first()?->total  ?? 0);
            $dailyExpense[] = (float) ($dayData->where('type', 'expense')->first()?->total ?? 0);
        }

        // ── Daftar bulan tersedia untuk filter ──────────────────
        $availableMonths = [];
        for ($i = 0; $i < 24; $i++) {
            $d = now()->subMonths($i);
            $availableMonths[] = [
                'month'      => $d->month,
                'year'       => $d->year,
                'label'      => $d->translatedFormat('F Y'),
                'selected'   => $d->month === $month && $d->year === $year,
            ];
        }

        return view('finance.laporan.index', compact(
            'month', 'year', 'monthName',
            'totalIncome', 'totalExpense', 'netBalance',
            'prevIncome', 'prevExpense', 'prevNet',
            'incomeChange', 'expenseChange',
            'incomeByCategory', 'expenseByCategory',
            'transactions', 'period', 'budgets',
            'dailyLabels', 'dailyIncome', 'dailyExpense',
            'availableMonths',
            'prevMonth', 'prevYear'
        ));
    }
}
