<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Finance\Transaction;
use App\Models\Finance\Account;
use App\Models\Finance\Category;
use App\Models\Finance\BudgetPeriod;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(private TransactionService $service) {}

    public function index(Request $request)
    {
        $month = $request->integer('month', now()->month);
        $year  = $request->integer('year', now()->year);

        $query = Transaction::with(['category', 'account'])
            ->whereNull('deleted_at')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        if ($request->filled('month') && $request->filled('year')) {
            $query->byPeriod($month, $year);
        }
        if ($request->filled('type'))        $query->where('type', $request->type);
        if ($request->filled('category_id')) $query->where('category_id', $request->category_id);

        $transactions = $query->paginate(20)->withQueryString();

        $summary = [
            'total_income'  => Transaction::income()->byPeriod($month, $year)->whereNull('deleted_at')->sum('amount'),
            'total_expense' => Transaction::expense()->byPeriod($month, $year)->whereNull('deleted_at')->sum('amount'),
            'kas_balance'   => Account::where('type', 'cash')->sum('balance'),
            'period'        => BudgetPeriod::where('month', $month)->where('year', $year)->first(),
        ];

        $categories = Category::where('is_active', true)->get();

        return view('finance.transactions.index', compact('transactions', 'summary', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $accounts   = Account::where('is_active', true)->get();
        $periods    = BudgetPeriod::where('status', 'active')
                        ->orderByDesc('year')
                        ->orderByDesc('month')
                        ->get();

        return view('finance.transactions.create', compact('categories', 'accounts', 'periods'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'account_id'       => 'required|exists:finance.accounts,id',
            'category_id'      => 'required|exists:finance.categories,id',
            'budget_period_id' => 'nullable|exists:finance.budget_periods,id',
            'type'             => 'required|in:income,expense',
            'amount'           => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'description'      => 'required|string|max:500',
            'notes'            => 'nullable|string',
            'reference'        => 'nullable|string|max:100',
            'attachment'       => 'nullable|image|mimes:jpeg,png,webp,gif|max:5120', // 5MB
        ]);

        // Upload foto nota/barang jika ada
        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')
                ->store('finance/attachments', 'public');
        }

        $validated['created_by']      = auth('finance')->id();
        $validated['created_by_name'] = auth('finance')->user()->name;

        $trx = $this->service->store($validated);

        return redirect()
            ->route('finance.transactions.index')
            ->with('success', "Transaksi {$trx->code} berhasil disimpan.");
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['category', 'account', 'budgetPeriod']);
        return view('finance.transactions.show', compact('transaction'));
    }

    public function destroy(Transaction $transaction)
    {
        $code = $transaction->code;
        $this->service->destroy($transaction);

        return redirect()
            ->route('finance.transactions.index')
            ->with('success', "Transaksi {$code} berhasil dihapus.");
    }
}