<?php

namespace App\Exports;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchasesExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping
{
    use Exportable;

    public function __construct(private readonly array $filters = [])
    {
    }

    public function query(): Builder
    {
        $query = Purchase::query()->with(['supplier', 'warehouse', 'admin', 'items']);

        if ($invoice = $this->filters['invoice_number'] ?? null) {
            $query->where('invoice_number', 'like', "%{$invoice}%");
        }

        if ($supplierId = $this->filters['supplier_id'] ?? null) {
            $query->where('supplier_id', $supplierId);
        }

        if ($warehouseId = $this->filters['warehouse_id'] ?? null) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($status = $this->filters['status'] ?? null) {
            $query->where('status', $status);
        }

        if ($from = $this->filters['date_from'] ?? null) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $this->filters['date_to'] ?? null) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            __('Invoice #'),
            __('Supplier'),
            __('Warehouse'),
            __('Date'),
            __('Items'),
            __('Total'),
            __('Discount'),
            __('Tax'),
            __('Net Total'),
            __('Status'),
            __('Admin'),
        ];
    }

    public function map($purchase): array
    {
        return [
            $purchase->invoice_number,
            $purchase->supplier?->name ?? '—',
            $purchase->warehouse?->name ?? '—',
            $purchase->created_at?->format('Y-m-d'),
            $purchase->items->count(),
            (float) $purchase->total_amount,
            (float) $purchase->discount_amount,
            (float) $purchase->tax_amount,
            (float) $purchase->net_amount,
            ucfirst($purchase->status),
            $purchase->admin?->name ?? '—',
        ];
    }
}
