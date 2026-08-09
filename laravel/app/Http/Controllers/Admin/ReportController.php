<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ArrayExport;
use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PayoutRequest;
use App\Models\Purchase;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('admin.reports.index');
    }

    public function sales(Request $request): View
    {
        [$from, $to] = $this->period($request);

        $orders = Order::query()->whereBetween('created_at', [$from, $to])->where('payment_status', 'paid');

        $totalOrders = (clone $orders)->count();
        $revenue = (float) (clone $orders)->sum('total_amount');
        $avgOrderValue = $totalOrders > 0 ? $revenue / $totalOrders : 0;

        $grouping = $request->string('group_by', 'daily')->toString();
        $format = match ($grouping) {
            'monthly' => '%Y-%m',
            'weekly' => '%x-%v',
            default => '%Y-%m-%d',
        };

        $chart = (clone $orders)
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as period, COUNT(*) as orders, SUM(total_amount) as revenue")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return view('admin.reports.sales', [
            'from' => $from, 'to' => $to, 'groupBy' => $grouping,
            'totalOrders' => $totalOrders, 'revenue' => $revenue, 'avgOrderValue' => $avgOrderValue,
            'chart' => $chart,
        ]);
    }

    public function purchases(Request $request): View
    {
        [$from, $to] = $this->period($request);

        $bySupplier = Purchase::query()
            ->whereBetween('created_at', [$from, $to])
            ->where('status', 'confirmed')
            ->selectRaw('supplier_id, COUNT(*) as purchases_count, SUM(net_amount) as total')
            ->groupBy('supplier_id')
            ->with('supplier')
            ->get();

        $total = (float) $bySupplier->sum('total');

        return view('admin.reports.purchases', ['from' => $from, 'to' => $to, 'bySupplier' => $bySupplier, 'total' => $total]);
    }

    public function expenses(Request $request): View
    {
        [$from, $to] = $this->period($request);

        $byCategory = Expense::query()
            ->whereBetween('date', [$from, $to])
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();

        $total = (float) $byCategory->sum('total');

        return view('admin.reports.expenses', ['from' => $from, 'to' => $to, 'byCategory' => $byCategory, 'total' => $total]);
    }

    public function profitLoss(Request $request): View
    {
        [$from, $to] = $this->period($request);

        $revenue = (float) Order::query()->whereBetween('created_at', [$from, $to])->where('payment_status', 'paid')->sum('total_amount');

        $cogs = (float) OrderItem::query()
            ->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$from, $to])->where('payment_status', 'paid'))
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->sum('products.cost_price');

        $expenses = (float) Expense::query()->whereBetween('date', [$from, $to])->sum('amount');

        $netProfit = $revenue - $cogs - $expenses;

        return view('admin.reports.profit-loss', compact('from', 'to', 'revenue', 'cogs', 'expenses', 'netProfit'));
    }

    public function affiliates(Request $request): View
    {
        [$from, $to] = $this->period($request);

        $earned = (float) AffiliateCommission::query()->whereBetween('created_at', [$from, $to])->sum('amount');
        $paid = (float) PayoutRequest::query()->whereBetween('processed_at', [$from, $to])->where('status', 'paid')->sum('amount');
        $pending = (float) AffiliateCommission::query()->where('status', 'pending')->sum('amount');

        $byAffiliate = AffiliateCommission::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('affiliate_id, SUM(amount) as total')
            ->groupBy('affiliate_id')
            ->with('affiliate.customer')
            ->get();

        return view('admin.reports.affiliates', compact('from', 'to', 'earned', 'paid', 'pending', 'byAffiliate'));
    }

    public function products(Request $request): View
    {
        [$from, $to] = $this->period($request);

        $topProducts = OrderItem::query()
            ->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$from, $to])->where('payment_status', 'paid'))
            ->selectRaw('product_id, product_name, SUM(qty) as qty_sold, SUM(total_price) as revenue')
            ->groupBy('product_id', 'product_name')
            ->orderByDesc('revenue')
            ->limit(20)
            ->get();

        return view('admin.reports.products', compact('from', 'to', 'topProducts'));
    }

    public function stock(): View
    {
        $stocks = Stock::query()->with(['product', 'warehouse'])->get();

        $valuation = $stocks->sum(fn (Stock $stock) => (float) ($stock->product?->cost_price ?? 0) * $stock->qty);

        return view('admin.reports.stock', ['stocks' => $stocks, 'valuation' => $valuation]);
    }

    public function customers(Request $request): View
    {
        [$from, $to] = $this->period($request);

        $newCustomers = Customer::query()->whereBetween('created_at', [$from, $to])->count();

        $repeatCustomers = Order::query()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('customer_id, COUNT(*) as orders_count')
            ->groupBy('customer_id')
            ->having('orders_count', '>', 1)
            ->get()
            ->count();

        $topCustomers = Order::query()
            ->where('payment_status', 'paid')
            ->selectRaw('customer_id, COUNT(*) as orders_count, SUM(total_amount) as ltv')
            ->groupBy('customer_id')
            ->orderByDesc('ltv')
            ->with('customer')
            ->limit(20)
            ->get();

        return view('admin.reports.customers', compact('from', 'to', 'newCustomers', 'repeatCustomers', 'topCustomers'));
    }

    public function export(Request $request, string $type)
    {
        [$headings, $rows] = $this->exportData($type, $request);

        $format = $request->string('format', 'excel')->toString();

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports._export-pdf', [
                'title' => __(ucfirst(str_replace('-', ' ', $type)).' Report'),
                'headings' => $headings,
                'rows' => $rows,
            ]);

            return $pdf->download($type.'-'.now()->format('Y-m-d-His').'.pdf');
        }

        return Excel::download(new ArrayExport($headings, $rows), $type.'-'.now()->format('Y-m-d-His').'.xlsx');
    }

    private function exportData(string $type, Request $request): array
    {
        [$from, $to] = $this->period($request);

        return match ($type) {
            'sales' => [
                [__('Date'), __('Orders'), __('Revenue')],
                Order::query()->whereBetween('created_at', [$from, $to])->where('payment_status', 'paid')
                    ->selectRaw("DATE(created_at) as d, COUNT(*) as c, SUM(total_amount) as r")
                    ->groupBy('d')->orderBy('d')->get()
                    ->map(fn ($row) => [$row->d, $row->c, (float) $row->r])->toArray(),
            ],
            'purchases' => [
                [__('Supplier'), __('Purchases'), __('Total')],
                Purchase::query()->whereBetween('created_at', [$from, $to])->where('status', 'confirmed')
                    ->selectRaw('supplier_id, COUNT(*) as c, SUM(net_amount) as t')->groupBy('supplier_id')->with('supplier')->get()
                    ->map(fn ($row) => [$row->supplier?->name ?? '—', $row->c, (float) $row->t])->toArray(),
            ],
            'expenses' => [
                [__('Category'), __('Total')],
                Expense::query()->whereBetween('date', [$from, $to])
                    ->selectRaw('category_id, SUM(amount) as t')->groupBy('category_id')->with('category')->get()
                    ->map(fn ($row) => [$row->category?->full_path ?? '—', (float) $row->t])->toArray(),
            ],
            'profit-loss' => [
                [__('Metric'), __('Amount')],
                (function () use ($from, $to) {
                    $revenue = (float) Order::query()->whereBetween('created_at', [$from, $to])->where('payment_status', 'paid')->sum('total_amount');
                    $cogs = (float) OrderItem::query()->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$from, $to])->where('payment_status', 'paid'))
                        ->join('products', 'products.id', '=', 'order_items.product_id')->sum('products.cost_price');
                    $expenses = (float) Expense::query()->whereBetween('date', [$from, $to])->sum('amount');

                    return [
                        [__('Revenue'), $revenue],
                        [__('COGS'), $cogs],
                        [__('Expenses'), $expenses],
                        [__('Net Profit'), $revenue - $cogs - $expenses],
                    ];
                })(),
            ],
            'affiliates' => [
                [__('Affiliate'), __('Commissions')],
                AffiliateCommission::query()->whereBetween('created_at', [$from, $to])
                    ->selectRaw('affiliate_id, SUM(amount) as t')->groupBy('affiliate_id')->with('affiliate.customer')->get()
                    ->map(fn ($row) => [$row->affiliate?->customer?->name ?? '—', (float) $row->t])->toArray(),
            ],
            'products' => [
                [__('Product'), __('Qty Sold'), __('Revenue')],
                OrderItem::query()->whereHas('order', fn ($q) => $q->whereBetween('created_at', [$from, $to])->where('payment_status', 'paid'))
                    ->selectRaw('product_name, SUM(qty) as q, SUM(total_price) as r')->groupBy('product_name')->orderByDesc('r')->limit(50)->get()
                    ->map(fn ($row) => [$row->product_name, (int) $row->q, (float) $row->r])->toArray(),
            ],
            'stock' => [
                [__('Product'), __('Warehouse'), __('Qty'), __('Valuation')],
                Stock::query()->with(['product', 'warehouse'])->get()
                    ->map(fn (Stock $stock) => [
                        $stock->product?->getTranslation('name', app()->getLocale()) ?? '—',
                        $stock->warehouse?->name ?? '—',
                        $stock->qty,
                        (float) ($stock->product?->cost_price ?? 0) * $stock->qty,
                    ])->toArray(),
            ],
            'customers' => [
                [__('Customer'), __('Orders'), __('LTV')],
                Order::query()->where('payment_status', 'paid')
                    ->selectRaw('customer_id, COUNT(*) as c, SUM(total_amount) as ltv')->groupBy('customer_id')->orderByDesc('ltv')->with('customer')->limit(50)->get()
                    ->map(fn ($row) => [$row->customer?->name ?? '—', $row->c, (float) $row->ltv])->toArray(),
            ],
            default => [[], []],
        };
    }

    private function period(Request $request): array
    {
        $from = $request->filled('date_from') ? Carbon::parse($request->string('date_from')->toString())->startOfDay() : now()->startOfMonth();
        $to = $request->filled('date_to') ? Carbon::parse($request->string('date_to')->toString())->endOfDay() : now()->endOfDay();

        return [$from, $to];
    }
}
