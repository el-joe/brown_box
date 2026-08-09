<?php

namespace App\Services;

use App\Models\AccountingEntry;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    public function create(array $data, int $adminId): Expense
    {
        return DB::transaction(function () use ($data, $adminId) {
            $expense = Expense::query()->create([...$data, 'admin_id' => $adminId]);

            $this->syncAccountingEntry($expense);

            return $expense;
        });
    }

    public function update(Expense $expense, array $data): Expense
    {
        return DB::transaction(function () use ($expense, $data) {
            $expense->update($data);

            $this->syncAccountingEntry($expense);

            return $expense->refresh();
        });
    }

    public function delete(Expense $expense): void
    {
        DB::transaction(function () use ($expense) {
            AccountingEntry::query()
                ->where('reference_type', Expense::class)
                ->where('reference_id', $expense->id)
                ->delete();

            $expense->delete();
        });
    }

    private function syncAccountingEntry(Expense $expense): void
    {
        AccountingEntry::query()->updateOrCreate(
            [
                'reference_type' => Expense::class,
                'reference_id' => $expense->id,
            ],
            [
                'type' => 'debit',
                'category' => 'expenses',
                'amount' => $expense->amount,
                'description' => $expense->description,
                'date' => $expense->date,
                'admin_id' => $expense->admin_id,
            ]
        );
    }
}
