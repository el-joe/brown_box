<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PurchasesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PurchaseRequest;
use App\Http\Requests\Admin\SupplierQuickRequest;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use RuntimeException;
use Yajra\DataTables\Facades\DataTables;

class PurchaseController extends Controller
{
    public function __construct(private readonly PurchaseService $purchases)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.purchases.index', [
            'filters' => $request->only(['invoice_number', 'supplier_id', 'warehouse_id', 'status', 'date_from', 'date_to']),
            'suppliers' => Supplier::query()->orderBy('name')->pluck('name', 'id'),
            'warehouses' => Warehouse::query()->active()->pluck('name', 'id'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Purchase::query()->with(['supplier', 'warehouse', 'admin'])->withCount('items');

        if ($invoice = $request->string('invoice_number')->toString()) {
            $query->where('invoice_number', 'like', "%{$invoice}%");
        }

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->integer('supplier_id'));
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->integer('warehouse_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        return DataTables::eloquent($query)
            ->addColumn('invoice_number', fn (Purchase $purchase) => e($purchase->invoice_number))
            ->addColumn('supplier', fn (Purchase $purchase) => e($purchase->supplier?->name ?? '—'))
            ->addColumn('warehouse', fn (Purchase $purchase) => e($purchase->warehouse?->name ?? '—'))
            ->addColumn('date', fn (Purchase $purchase) => $purchase->created_at?->format('Y-m-d'))
            ->addColumn('items_count', fn (Purchase $purchase) => (int) $purchase->items_count)
            ->addColumn('net_amount', fn (Purchase $purchase) => number_format((float) $purchase->net_amount, 2))
            ->addColumn('status', fn (Purchase $purchase) => view('admin.purchases._status', ['purchase' => $purchase])->render())
            ->addColumn('admin', fn (Purchase $purchase) => e($purchase->admin?->name ?? '—'))
            ->addColumn('actions', fn (Purchase $purchase) => view('admin.purchases._actions', ['purchase' => $purchase])->render())
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }

    public function export(Request $request)
    {
        $filters = $request->only(['invoice_number', 'supplier_id', 'warehouse_id', 'status', 'date_from', 'date_to']);

        return Excel::download(new PurchasesExport($filters), 'purchases-'.now()->format('Y-m-d-His').'.xlsx');
    }

    public function create(): View
    {
        return view('admin.purchases.create', [
            'purchase' => new Purchase(),
            'suppliers' => Supplier::query()->orderBy('name')->pluck('name', 'id'),
            'warehouses' => Warehouse::query()->active()->pluck('name', 'id'),
            'invoiceNumber' => $this->purchases->nextInvoiceNumber(),
        ]);
    }

    public function store(PurchaseRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['supplier_id', 'warehouse_id', 'invoice_number', 'discount_amount', 'tax_amount', 'notes', 'status']);

        $purchase = $this->purchases->create($data, $request->input('items', []), auth('admin')->id());

        return redirect()->route('admin.purchases.show', $purchase)->with('success', __('Purchase saved successfully.'));
    }

    public function show(int $id): View
    {
        $purchase = $this->purchases->find($id)?->load(['supplier', 'warehouse', 'admin', 'items.product', 'items.variant']);

        abort_if(! $purchase, 404);

        return view('admin.purchases.show', ['purchase' => $purchase]);
    }

    public function confirm(int $id): RedirectResponse
    {
        try {
            $this->purchases->confirm($id, auth('admin')->id());
        } catch (RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->route('admin.purchases.show', $id)->with('success', __('Purchase confirmed and stock updated.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->purchases->delete($id);
        } catch (RuntimeException $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }

        return redirect()->route('admin.purchases.index')->with('success', __('Purchase deleted successfully.'));
    }

    public function searchProducts(Request $request): JsonResponse
    {
        $keyword = $request->string('q')->toString();

        $products = Product::query()
            ->with(['variants' => fn ($q) => $q->active()])
            ->search($keyword)
            ->active()
            ->limit(20)
            ->get();

        return response()->json($products->map(fn (Product $product) => [
            'id' => $product->id,
            'text' => $product->getTranslation('name', app()->getLocale()).' ('.$product->sku.')',
            'sku' => $product->sku,
            'cost_price' => (float) $product->cost_price,
            'has_variants' => (bool) $product->has_variants,
            'variants' => $product->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'cost_price' => (float) $variant->cost_price,
            ])->values(),
        ])->values());
    }

    public function quickAddSupplier(SupplierQuickRequest $request): JsonResponse
    {
        $supplier = Supplier::query()->create($request->validated());

        return response()->json(['id' => $supplier->id, 'text' => $supplier->name]);
    }
}
