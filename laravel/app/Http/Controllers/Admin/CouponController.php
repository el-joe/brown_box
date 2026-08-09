<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponRequest;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Product;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CouponController extends Controller
{
    public function __construct(private readonly CouponService $coupons)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.coupons.index', [
            'filters' => $request->only(['code', 'type', 'is_active', 'valid_now']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Coupon::query();

        if ($code = $request->string('code')->toString()) {
            $query->where('code', 'like', "%{$code}%");
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type')->toString());
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->boolean('valid_now')) {
            $query->valid();
        }

        return DataTables::eloquent($query)
            ->addColumn('code', fn (Coupon $coupon) => e($coupon->code))
            ->addColumn('type', fn (Coupon $coupon) => view('admin.coupons._type', ['coupon' => $coupon])->render())
            ->addColumn('value', fn (Coupon $coupon) => match ($coupon->type) {
                'percentage' => rtrim(rtrim(number_format((float) $coupon->value, 2), '0'), '.').'%',
                'fixed' => money_format((float) $coupon->value),
                default => '—',
            })
            ->addColumn('min_order', fn (Coupon $coupon) => $coupon->min_order_amount !== null ? money_format((float) $coupon->min_order_amount) : '—')
            ->addColumn('usage', fn (Coupon $coupon) => $coupon->used_count.' / '.($coupon->max_uses ?? __('Unlimited')))
            ->addColumn('validity', fn (Coupon $coupon) => trim(
                ($coupon->starts_at?->format('Y-m-d') ?? __('Any')).' → '.($coupon->expires_at?->format('Y-m-d') ?? __('No expiry'))
            ))
            ->addColumn('status', fn (Coupon $coupon) => view('admin.coupons._status', ['coupon' => $coupon])->render())
            ->addColumn('actions', fn (Coupon $coupon) => view('admin.coupons._actions', ['coupon' => $coupon])->render())
            ->rawColumns(['type', 'status', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.coupons.form', $this->formData(new Coupon()));
    }

    public function store(CouponRequest $request): RedirectResponse
    {
        $this->coupons->create($this->mapData($request));

        return redirect()->route('admin.coupons.index')->with('success', __('Coupon created successfully.'));
    }

    public function edit(int $id): View
    {
        $coupon = $this->coupons->find($id);

        abort_if(! $coupon, 404);

        $coupon->load(['products', 'categories']);

        return view('admin.coupons.form', $this->formData($coupon));
    }

    public function update(CouponRequest $request, int $id): RedirectResponse
    {
        $this->coupons->update($id, $this->mapData($request));

        return redirect()->route('admin.coupons.index')->with('success', __('Coupon updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->coupons->delete($id);

        return redirect()->route('admin.coupons.index')->with('success', __('Coupon deleted successfully.'));
    }

    public function validateCoupon(CouponRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $coupon = $this->coupons->find($id);

        abort_if(! $coupon, 404);

        $coupon = $this->coupons->update($id, ['is_active' => ! $coupon->is_active] + $this->preserve($coupon));

        return response()->json(['success' => true, 'is_active' => $coupon->is_active]);
    }

    public function generateCode(): JsonResponse
    {
        return response()->json(['code' => 'CPN-'.strtoupper(Str::random(8))]);
    }

    private function mapData(CouponRequest $request): array
    {
        $data = $request->safe()->only([
            'code', 'type', 'value', 'min_order_amount', 'max_uses',
            'starts_at', 'expires_at', 'apply_to', 'product_ids', 'category_ids',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function preserve(Coupon $coupon): array
    {
        return [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value,
            'apply_to' => $coupon->categories()->exists() ? 'specific_categories' : ($coupon->products()->exists() ? 'specific_products' : 'all'),
            'product_ids' => $coupon->products()->pluck('products.id')->all(),
            'category_ids' => $coupon->categories()->pluck('categories.id')->all(),
        ];
    }

    private function formData(Coupon $coupon): array
    {
        return [
            'coupon' => $coupon,
            'products' => Product::query()->active()->get()->mapWithKeys(
                fn (Product $product) => [$product->id => $product->getTranslation('name', app()->getLocale()).' ('.$product->sku.')']
            ),
            'categories' => Category::query()->active()->get()->mapWithKeys(
                fn (Category $category) => [$category->id => $category->getTranslation('name', app()->getLocale())]
            ),
        ];
    }
}
