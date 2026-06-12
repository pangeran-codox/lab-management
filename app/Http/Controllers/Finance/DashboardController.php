<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Transaction;
use App\Models\Finance\Account;
use App\Models\Finance\BudgetPeriod;
use App\Models\Finance\Budget;

class DashboardController extends Controller
{
    public function index()
    {
        $month = now()->month;
        $year  = now()->year;

        $totalIncome  = Transaction::income()->byPeriod($month, $year)->whereNull('deleted_at')->sum('amount');
        $totalExpense = Transaction::expense()->byPeriod($month, $year)->whereNull('deleted_at')->sum('amount');
        $kasBalance   = Account::where('type', 'cash')->where('is_active', true)->sum('balance');
        $period       = BudgetPeriod::where('month', $month)->where('year', $year)->first();

        $budgets = $period
            ? Budget::with('category')->where('budget_period_id', $period->id)->get()
            : collect();

        $recentTransactions = Transaction::with(['category', 'account'])
            ->whereNull('deleted_at')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // ── Bar Chart: 6 bulan terakhir (Optimized into 2 queries) ───────────────────
        $sixMonthsAgo = now()->subMonths(5)->startOfMonth();
        
        $incomeData = Transaction::income()
            ->where('transaction_date', '>=', $sixMonthsAgo)
            ->whereNull('deleted_at')
            ->selectRaw("TO_CHAR(transaction_date, 'Mon YY') as month_year, SUM(amount) as total, TO_CHAR(transaction_date, 'YYYY-MM') as sort_key")
            ->groupBy('month_year', 'sort_key')
            ->orderBy('sort_key')
            ->get()
            ->pluck('total', 'month_year');

        $expenseData = Transaction::expense()
            ->where('transaction_date', '>=', $sixMonthsAgo)
            ->whereNull('deleted_at')
            ->selectRaw("TO_CHAR(transaction_date, 'Mon YY') as month_year, SUM(amount) as total, TO_CHAR(transaction_date, 'YYYY-MM') as sort_key")
            ->groupBy('month_year', 'sort_key')
            ->orderBy('sort_key')
            ->get()
            ->pluck('total', 'month_year');

        $chartMonths  = [];
        $chartIncome  = [];
        $chartExpense = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $label = $date->isoFormat('MMM YY');
            
            $chartMonths[]  = $label;
            $chartIncome[]  = (float) ($incomeData[$label] ?? 0);
            $chartExpense[] = (float) ($expenseData[$label] ?? 0);
        }

        // ── Pie Chart: pengeluaran per kategori bulan ini ─
        $expenseByCategory = Transaction::with('category')
            ->expense()
            ->byPeriod($month, $year)
            ->whereNull('deleted_at')
            ->get()
            ->groupBy('category_id')
            ->map(fn($trx) => [
                'name'  => $trx->first()->category->name,
                'total' => (float) $trx->sum('amount'),
                'color' => $trx->first()->category->color,
            ])
            ->values();

        return view('finance.dashboard.index', compact(
            'totalIncome', 'totalExpense', 'kasBalance',
            'period', 'budgets', 'recentTransactions',
            'chartMonths', 'chartIncome', 'chartExpense',
            'expenseByCategory'
        ));
    }
}