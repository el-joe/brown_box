<?php

namespace App\Exports;

use App\Models\Expense;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpensesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(private readonly array $filters = [])
    {
    }

    public function query(): Builder
    {
        $query = Expense::query()->with(['category', 'admin']);

        if ($categoryId = $this->filters['category_id'] ?? null) {
            $query->where('category_id', $categoryId);
        }

        if ($paymentMethod = $this->filters['payment_method'] ?? null) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($from = $this->filters['date_from'] ?? null) {
            $query->whereDate('date', '>=', $from);
        }

        if ($to = $this->filters['date_to'] ?? null) {
            $query->whereDate('date', '<=', $to);
        }

        if ($min = $this->filters['min_amount'] ?? null) {
            $query->where('amount', '>=', $min);
        }

        if ($max = $this->filters['max_amount'] ?? null) {
            $query->where('amount', '<=', $max);
        }

        return $query->latest('date');
    }

    public function headings(): array
    {
        return [
            __('Date'),
            __('Category'),
            __('Description'),
            __('Amount'),
            __('Payment Method'),
            __('Reference'),
            __('Admin'),
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->date?->format('Y-m-d'),
            $expense->category?->full_path ?? '—',
            $expense->description,
            (float) $expense->amount,
            ucfirst($expense->payment_method),
            $expense->reference,
            $expense->admin?->name ?? '—',
        ];
    }
}
