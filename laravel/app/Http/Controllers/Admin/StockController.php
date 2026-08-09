<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockAdjustRequest;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    public function __construct(private readonly StockService $stocks)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.stock.index', [
            'filters' => $request->only(['product', 'warehouse_id', 'status']),
            'warehouses' => Warehouse::query()->active()->pluck('name', 'id'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Stock::query()->with(['product.productImages', 'variant', 'warehouse']);

        if ($product = $request->string('product')->toString()) {
            $query->whereHas('product', function ($q) use ($product) {
                $q->where('sku', 'like', "%{$product}%")
                    ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ['%'.mb_strtolower($product).'%'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.ar")) LIKE ?', ['%'.mb_strtolower($product).'%']);
            });
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->integer('warehouse_id'));
        }

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            $query->when($status === 'low', fn ($q) => $q->lowStock()->where('qty', '>', 0))
                ->when($status === 'out', fn ($q) => $q->where('qty', '<=', 0));
        }

        return DataTables::eloquent($query)
            ->addColumn('image', fn (Stock $stock) => $stock->product?->main_image
                ? '<img src="'.e(asset_url($stock->product->main_image->path)).'" class="w-10 h-10 rounded-lg object-cover border border-slate-200">'
                : '<div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300"><i class="fa-solid fa-box"></i></div>')
            ->addColumn('product', fn (Stock $stock) => e($stock->product?->getTranslation('name', app()->getLocale()) ?? '—'))
            ->addColumn('variant', fn (Stock $stock) => e($stock->variant?->sku ?? '—'))
            ->addColumn('warehouse', fn (Stock $stock) => e($stock->warehouse?->name ?? '—'))
            ->addColumn('qty', fn (Stock $stock) => (int) $stock->qty)
            ->addColumn('min_qty_alert', fn (Stock $stock) => (int) $stock->min_qty_alert)
            ->addColumn('status', fn (Stock $stock) => view('admin.stock._status', ['stock' => $stock])->render())
            ->addColumn('actions', fn (Stock $stock) => view('admin.stock._actions', ['stock' => $stock])->render())
            ->rawColumns(['image', 'status', 'actions'])
            ->toJson();
    }

    public function adjustForm(int $id): View
    {
        $stock = Stock::query()->with(['product', 'variant', 'warehouse'])->findOrFail($id);

        return view('admin.stock.adjust', [
            'stock' => $stock,
            'warehouses' => Warehouse::query()->active()->pluck('name', 'id'),
        ]);
    }

    public function adjust(StockAdjustRequest $request, int $id): RedirectResponse
    {
        $stock = Stock::query()->findOrFail($id);

        try {
            $this->stocks->adjust(
                productId: $stock->product_id,
                warehouseId: $request->integer('warehouse_id'),
                qty: $request->integer('qty'),
                direction: $request->string('direction')->toString(),
                variantId: $stock->variant_id,
                adminId: auth('admin')->id(),
                note: $request->string('note')->toString() ?: null,
            );
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['qty' => $e->getMessage()]);
        }

        return redirect()->route('admin.stock.index')->with('success', __('Stock adjusted successfully.'));
    }

    public function movements(Request $request): View
    {
        return view('admin.stock.movements', [
            'filters' => $request->only(['product', 'warehouse_id', 'type', 'date_from', 'date_to']),
            'warehouses' => Warehouse::query()->active()->pluck('name', 'id'),
        ]);
    }

    public function movementsData(Request $request): JsonResponse
    {
        $query = StockMovement::query()->with(['product', 'variant', 'warehouse', 'admin', 'reference']);

        if ($product = $request->string('product')->toString()) {
            $query->whereHas('product', function ($q) use ($product) {
                $q->where('sku', 'like', "%{$product}%")
                    ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ['%'.mb_strtolower($product).'%'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.ar")) LIKE ?', ['%'.mb_strtolower($product).'%']);
            });
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->integer('warehouse_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        return DataTables::eloquent($query)
            ->orderColumn('created_at', 'created_at $1')
            ->addColumn('date', fn (StockMovement $movement) => $movement->created_at?->format('Y-m-d H:i'))
            ->addColumn('product', fn (StockMovement $movement) => e($movement->product?->getTranslation('name', app()->getLocale()) ?? '—'))
            ->addColumn('variant', fn (StockMovement $movement) => e($movement->variant?->sku ?? '—'))
            ->addColumn('warehouse', fn (StockMovement $movement) => e($movement->warehouse?->name ?? '—'))
            ->addColumn('type', fn (StockMovement $movement) => view('admin.stock._type-badge', ['movement' => $movement])->render())
            ->addColumn('qty_change', fn (StockMovement $movement) => ($movement->qty > 0 ? '+' : '').(int) $movement->qty)
            ->addColumn('before', fn (StockMovement $movement) => (int) $movement->before_qty)
            ->addColumn('after', fn (StockMovement $movement) => (int) $movement->after_qty)
            ->addColumn('reference', fn (StockMovement $movement) => view('admin.stock._reference', ['movement' => $movement])->render())
            ->addColumn('note', fn (StockMovement $movement) => e($movement->note ?? '—'))
            ->addColumn('admin', fn (StockMovement $movement) => e($movement->admin?->name ?? '—'))
            ->rawColumns(['type', 'reference'])
            ->toJson();
    }
}
