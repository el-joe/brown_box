<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\FlashSaleRequest;
use App\Models\FlashSale;
use App\Models\Product;
use App\Services\FlashSaleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class FlashSaleController extends Controller
{
    public function __construct(private readonly FlashSaleService $flashSales)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.flash-sales.index', [
            'filters' => $request->only(['name', 'status']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = FlashSale::query()->withCount('items');

        if ($name = $request->string('name')->toString()) {
            $query->where(function ($q) use ($name) {
                $q->whereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ['%'.mb_strtolower($name).'%'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.ar")) LIKE ?', ['%'.mb_strtolower($name).'%']);
            });
        }

        $now = now();

        if ($request->filled('status')) {
            $status = $request->string('status')->toString();
            $query->when($status === 'active', fn ($q) => $q->where('is_active', true)->where('starts_at', '<=', $now)->where('ends_at', '>=', $now))
                ->when($status === 'upcoming', fn ($q) => $q->where('starts_at', '>', $now))
                ->when($status === 'ended', fn ($q) => $q->where('ends_at', '<', $now));
        }

        return DataTables::eloquent($query)
            ->addColumn('name', fn (FlashSale $flashSale) => e($flashSale->getTranslation('name', app()->getLocale())))
            ->addColumn('starts_at', fn (FlashSale $flashSale) => $flashSale->starts_at->format('Y-m-d H:i'))
            ->addColumn('ends_at', fn (FlashSale $flashSale) => $flashSale->ends_at->format('Y-m-d H:i'))
            ->addColumn('items_count', fn (FlashSale $flashSale) => (int) $flashSale->items_count)
            ->addColumn('status', fn (FlashSale $flashSale) => view('admin.flash-sales._status', ['flashSale' => $flashSale])->render())
            ->addColumn('actions', fn (FlashSale $flashSale) => view('admin.flash-sales._actions', ['flashSale' => $flashSale])->render())
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.flash-sales.form', [
            'flashSale' => new FlashSale(),
        ]);
    }

    public function store(FlashSaleRequest $request): RedirectResponse
    {
        $this->flashSales->create($this->mapData($request));

        return redirect()->route('admin.flash-sales.index')->with('success', __('Flash sale created successfully.'));
    }

    public function edit(int $id): View
    {
        $flashSale = $this->flashSales->find($id);

        abort_if(! $flashSale, 404);

        $flashSale->load(['items.product', 'items.variant']);

        return view('admin.flash-sales.form', [
            'flashSale' => $flashSale,
        ]);
    }

    public function update(FlashSaleRequest $request, int $id): RedirectResponse
    {
        $this->flashSales->update($id, $this->mapData($request));

        return redirect()->route('admin.flash-sales.index')->with('success', __('Flash sale updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->flashSales->delete($id);

        return redirect()->route('admin.flash-sales.index')->with('success', __('Flash sale deleted successfully.'));
    }

    public function validateFlashSale(FlashSaleRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $flashSale = $this->flashSales->find($id);

        abort_if(! $flashSale, 404);

        $flashSale->update(['is_active' => ! $flashSale->is_active]);

        return response()->json(['success' => true, 'is_active' => $flashSale->is_active]);
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
            'price' => (float) $product->price,
            'has_variants' => (bool) $product->has_variants,
            'variants' => $product->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => (float) ($variant->price ?? $product->price),
            ])->values(),
        ])->values());
    }

    private function mapData(FlashSaleRequest $request): array
    {
        $data = $request->safe()->only(['name', 'starts_at', 'ends_at', 'items']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
