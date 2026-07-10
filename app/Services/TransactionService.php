<?php

namespace App\Services;

use App\Models\Finance\Transaction;
use App\Models\Finance\Account;
use App\Models\Finance\Budget;
use App\Models\Finance\BudgetPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionService
{
    public function __construct(private WhatsAppService $wa) {}

    public function store(array $data): Transaction
    {
        return DB::connection('finance')->transaction(function () use ($data) {

            // 1. Generate kode
            $data['code'] = Transaction::generateCode();

            // 2. Simpan transaksi
            $trx = Transaction::create($data);

            // 3. Update saldo akun
            $account = Account::find($trx->account_id);
            if ($trx->type === 'income') {
                $account->increment('balance', $trx->amount);
            } else {
                $account->decrement('balance', $trx->amount);
            }
            $account->refresh();

            // 4. Update used_amount di budget (khusus pengeluaran)
            $budget = null;
            if ($trx->type === 'expense' && $trx->budget_period_id) {
                $budget = Budget::where('budget_period_id', $trx->budget_period_id)
                                ->where('category_id', $trx->category_id)
                                ->first();
                if ($budget) {
                    $used = Transaction::where('budget_period_id', $trx->budget_period_id)
                                       ->where('category_id', $trx->category_id)
                                       ->whereNull('deleted_at')
                                       ->where('type', 'expense')
                                       ->sum('amount');
                    $budget->update(['used_amount' => $used]);
                    $budget->refresh();
                }
            }

            // 5. Kirim notifikasi WA
            $this->sendNotification($trx, $account, $budget);

            return $trx->load(['category', 'account', 'budgetPeriod']);
        });
    }

    public function destroy(Transaction $trx): bool
    {
        return DB::connection('finance')->transaction(function () use ($trx) {

            // Koreksi saldo (balik arah)
            $account = Account::find($trx->account_id);
            if ($trx->type === 'income') {
                $account->decrement('balance', $trx->amount);
            } else {
                $account->increment('balance', $trx->amount);
            }

            // Koreksi used_amount di budget
            if ($trx->type === 'expense' && $trx->budget_period_id) {
                $budget = Budget::where('budget_period_id', $trx->budget_period_id)
                                ->where('category_id', $trx->category_id)
                                ->first();
                if ($budget) {
                    $used = Transaction::where('budget_period_id', $trx->budget_period_id)
                                       ->where('category_id', $trx->category_id)
                                       ->whereNull('deleted_at')
                                       ->where('type', 'expense')
                                       ->where('id', '!=', $trx->id)
                                       ->sum('amount');
                    $budget->update(['used_amount' => $used]);
                }
            }

            // Hapus file lampiran fisik (jika ada)
            if ($trx->attachment) {
                Storage::disk('public')->delete($trx->attachment);
            }

            return $trx->delete();
        });
    }

    private function sendNotification(Transaction $trx, Account $account, ?Budget $budget): void
    {
        $payload = [
            'transaction_id' => $trx->id,
            'code'           => $trx->code,
            'category'       => $trx->category->name,
            'amount'         => $trx->amount,
            'date'           => $trx->transaction_date->format('d/m/Y'),
            'description'    => $trx->description,
            'created_by'     => $trx->created_by_name ?? 'Sistem',
            'balance'        => $account->balance,
        ];

        if ($trx->type === 'income') {
            $this->wa->notifyIncome($payload);
        } else {
            if ($budget) {
                $payload['budget_amount']    = $budget->amount;
                $payload['budget_used']      = $budget->used_amount;
                $payload['budget_remaining'] = $budget->remaining;
            }

            $this->wa->notifyExpense($payload);

            // Kirim warning jika anggaran hampir habis
            if ($budget && $this->wa->shouldWarnBudget($budget->used_amount, $budget->amount)) {
                $period = BudgetPeriod::find($trx->budget_period_id);
                $this->wa->notifyBudgetWarning([
                    'category'  => $trx->category->name,
                    'period'    => $period?->name ?? '-',
                    'amount'    => $budget->amount,
                    'used'      => $budget->used_amount,
                    'remaining' => $budget->remaining,
                ]);
            }
        }
    }
}