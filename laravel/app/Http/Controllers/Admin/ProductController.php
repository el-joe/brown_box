<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductService $products,
        private readonly ProductRepositoryInterface $productRepository,
    ) {
    }

    public function index(Request $request): View
    {
        return view('admin.products.index', [
            'filters' => $request->only([
                'search', 'category_id', 'brand_id', 'min_price', 'max_price',
                'stock_status', 'has_variants', 'is_active', 'is_featured',
            ]),
            'categories' => $this->categoryOptions(),
            'brands' => Brand::query()->active()->get()->pluck('name', 'id'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Product::query()->with(['category', 'brand', 'stocks', 'productImages'])->withCount('variants');

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                    ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ['%'.mb_strtolower($search).'%'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.ar")) LIKE ?', ['%'.mb_strtolower($search).'%']);
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('brand_id')) {
            $query->where('brand_id', $request->integer('brand_id'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        if ($request->filled('has_variants')) {
            $query->where('has_variants', $request->boolean('has_variants'));
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        if ($request->filled('stock_status')) {
            $query->when($request->string('stock_status')->toString() === 'in', fn ($q) => $q->whereHas('stocks', fn ($s) => $s->havingRaw('SUM(qty) > 0')->groupBy('product_id')))
                ->when($request->string('stock_status')->toString() === 'out', fn ($q) => $q->whereDoesntHave('stocks', fn ($s) => $s->havingRaw('SUM(qty) > 0')->groupBy('product_id')));
        }

        return DataTables::eloquent($query)
            ->addColumn('image', fn (Product $product) => $product->main_image
                ? '<img src="'.e(asset_url($product->main_image->path)).'" class="w-10 h-10 rounded-lg object-cover border border-slate-200">'
                : '<div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300"><i class="fa-solid fa-box"></i></div>')
            ->addColumn('name', fn (Product $product) => e($product->getTranslation('name', app()->getLocale())))
            ->addColumn('sku', fn (Product $product) => e($product->sku ?? '—'))
            ->addColumn('category', fn (Product $product) => e($product->category?->getTranslation('name', app()->getLocale()) ?? '—'))
            ->addColumn('brand', fn (Product $product) => e($product->brand?->getTranslation('name', app()->getLocale()) ?? '—'))
            ->addColumn('price', fn (Product $product) => money_format((float) $product->price))
            ->addColumn('stock', fn (Product $product) => (int) $product->stocks->sum('qty'))
            ->addColumn('status', fn (Product $product) => view('admin.products._status', ['product' => $product])->render())
            ->addColumn('featured', fn (Product $product) => view('admin.products._featured', ['product' => $product])->render())
            ->addColumn('actions', fn (Product $product) => view('admin.products._actions', ['product' => $product])->render())
            ->rawColumns(['image', 'status', 'featured', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.products.create', $this->formData(new Product()));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $this->products->store($this->mapData($request));

        return redirect()->route('admin.products.index')->with('success', __('Product created successfully.'));
    }

    public function edit(int $id): View
    {
        $product = $this->productRepository->find($id);

        abort_if(! $product, 404);

        $product->load(['tags', 'productImages', 'variants.variantAttributes', 'seoPage']);

        return view('admin.products.edit', $this->formData($product));
    }

    public function update(ProductRequest $request, int $id): RedirectResponse
    {
        $this->products->updateProduct($id, $this->mapData($request));

        return redirect()->route('admin.products.index')->with('success', __('Product updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->products->delete($id);

        return redirect()->route('admin.products.index')->with('success', __('Product deleted successfully.'));
    }

    public function validateProduct(ProductRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        abort_if(! $product, 404);

        $product = $this->products->update($id, ['is_active' => ! $product->is_active]);

        return response()->json(['success' => true, 'is_active' => $product->is_active]);
    }

    public function toggleFeatured(int $id): JsonResponse
    {
        $product = $this->productRepository->find($id);

        abort_if(! $product, 404);

        $product = $this->products->update($id, ['is_featured' => ! $product->is_featured]);

        return response()->json(['success' => true, 'is_featured' => $product->is_featured]);
    }

    public function generateSku(): JsonResponse
    {
        return response()->json(['sku' => 'PRD-'.strtoupper(Str::random(8))]);
    }

    private function mapData(ProductRequest $request): array
    {
        $data = $request->safe()->only([
            'category_id', 'brand_id', 'name', 'description', 'short_description',
            'sku', 'barcode', 'price', 'compare_price', 'cost_price',
            'weight', 'width', 'height', 'length',
            'meta_title', 'meta_description', 'meta_keywords',
            'tags', 'variants', 'video_url',
            'og_title', 'og_description', 'robots', 'canonical_url',
        ]);

        $data['existing_image_ids'] = json_decode((string) $request->input('existing_image_ids', '[]'), true) ?: [];
        $data['primary_existing_image_id'] = $request->filled('primary_existing_image_id') ? $request->integer('primary_existing_image_id') : null;
        $data['primary_new_image_index'] = $request->filled('primary_new_image_index') ? $request->integer('primary_new_image_index') : null;

        $data['is_active'] = $request->boolean('is_active');
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_digital'] = $request->boolean('is_digital');
        $data['has_variants'] = $request->boolean('has_variants');

        if ($request->hasFile('images')) {
            $data['images'] = $request->file('images');
        }

        if ($request->hasFile('og_image')) {
            $data['og_image'] = $request->file('og_image')->store('seo', 'public');
        }

        if ($request->filled('schema_json')) {
            $data['schema_json'] = json_decode((string) $request->input('schema_json'), true);
        }

        if ($request->has('variants')) {
            $variants = $request->input('variants', []);

            foreach ($variants as $index => $variant) {
                if ($request->hasFile("variants.{$index}.image")) {
                    $variants[$index]['image'] = $request->file("variants.{$index}.image");
                }
            }

            $data['variants'] = $variants;
        }

        return $data;
    }

    private function formData(Product $product): array
    {
        return [
            'product' => $product,
            'categories' => $this->categoryOptions(),
            'brands' => Brand::query()->active()->get()->pluck('name', 'id'),
            'attributes' => ProductAttribute::query()->with('values')->get(),
        ];
    }

    private function categoryOptions(): \Illuminate\Support\Collection
    {
        return Category::query()->active()->get()->mapWithKeys(function (Category $category) {
            $prefix = $category->parent_id ? '— ' : '';

            return [$category->id => $prefix.$category->getTranslation('name', app()->getLocale())];
        });
    }
}
