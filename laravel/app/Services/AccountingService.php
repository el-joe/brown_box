<?php

namespace App\Services;

use App\Models\AccountingEntry;
use App\Models\Order;
use App\Repositories\Contracts\AccountingEntryRepositoryInterface;
use Illuminate\Database\Eloquent\Model;

class AccountingService
{
    public function __construct(private readonly AccountingEntryRepositoryInterface $entries)
    {
    }

    /**
     * Record a sales income entry for a verified-payment order. Order
     * creation already records income for the order total, so this is a
     * no-op if a sales entry for the order already exists.
     */
    public function recordSale(Order $order): ?Model
    {
        $exists = AccountingEntry::query()
            ->where('reference_type', 'order')
            ->where('reference_id', $order->id)
            ->where('category', 'sales')
            ->exists();

        if ($exists) {
            return null;
        }

        return $this->recordIncome(
            category: 'sales',
            amount: (float) $order->total_amount,
            description: "Order #{$order->order_number}",
            referenceType: 'order',
            referenceId: $order->id,
        );
    }

    public function createEntry(array $data): Model
    {
        return $this->entries->create($data);
    }

    public function recordIncome(string $category, float $amount, string $description, ?string $referenceType = null, ?int $referenceId = null, ?int $adminId = null): Model
    {
        return $this->createEntry([
            'type' => 'income',
            'category' => $category,
            'amount' => $amount,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'date' => now()->toDateString(),
            'admin_id' => $adminId,
        ]);
    }

    public function recordExpense(string $category, float $amount, string $description, ?string $referenceType = null, ?int $referenceId = null, ?int $adminId = null): Model
    {
        return $this->createEntry([
            'type' => 'expense',
            'category' => $category,
            'amount' => $amount,
            'description' => $description,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'date' => now()->toDateString(),
            'admin_id' => $adminId,
        ]);
    }

    /**
     * Net balance (income - expense) for a given date range. Omit dates for all-time.
     */
    public function balance(?string $from = null, ?string $to = null): float
    {
        $income = $this->entries->totalByTypeBetween('income', $from, $to);
        $expense = $this->entries->totalByTypeBetween('expense', $from, $to);

        return round($income - $expense, 2);
    }

    public function totalIncome(?string $from = null, ?string $to = null): float
    {
        return $this->entries->totalByTypeBetween('income', $from, $to);
    }

    public function totalExpense(?string $from = null, ?string $to = null): float
    {
        return $this->entries->totalByTypeBetween('expense', $from, $to);
    }
}
