<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BrandRequest;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    public function __construct(private readonly BrandService $brands)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.brands.index', [
            'filters' => $request->only(['name', 'is_active']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Brand::query()->withCount('products');

        if ($name = $request->string('name')->toString()) {
            $query->where(function ($q) use ($name) {
                $q->whereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ['%'.mb_strtolower($name).'%'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.ar")) LIKE ?', ['%'.mb_strtolower($name).'%']);
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return DataTables::eloquent($query)
            ->addColumn('logo', fn (Brand $brand) => $brand->logo
                ? '<img src="'.e(asset_url($brand->logo)).'" class="w-10 h-10 rounded-lg object-cover border border-slate-200">'
                : '<div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300"><i class="fa-solid fa-copyright"></i></div>')
            ->addColumn('name_en', fn (Brand $brand) => e($brand->getTranslation('name', 'en')))
            ->addColumn('name_ar', fn (Brand $brand) => e($brand->getTranslation('name', 'ar')))
            ->addColumn('products_count', fn (Brand $brand) => (int) $brand->products_count)
            ->addColumn('status', fn (Brand $brand) => view('admin.brands._status', ['brand' => $brand])->render())
            ->addColumn('actions', fn (Brand $brand) => view('admin.brands._actions', ['brand' => $brand])->render())
            ->rawColumns(['logo', 'status', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.brands.form', [
            'brand' => new Brand(),
        ]);
    }

    public function store(BrandRequest $request): RedirectResponse
    {
        $this->brands->create($this->mapData($request));

        return redirect()->route('admin.brands.index')->with('success', __('Brand created successfully.'));
    }

    public function edit(int $id): View
    {
        $brand = $this->brands->find($id);

        abort_if(! $brand, 404);

        return view('admin.brands.form', [
            'brand' => $brand,
        ]);
    }

    public function update(BrandRequest $request, int $id): RedirectResponse
    {
        $this->brands->update($id, $this->mapData($request));

        return redirect()->route('admin.brands.index')->with('success', __('Brand updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->brands->delete($id);

        return redirect()->route('admin.brands.index')->with('success', __('Brand deleted successfully.'));
    }

    public function validateBrand(BrandRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $brand = $this->brands->find($id);

        abort_if(! $brand, 404);

        $brand = $this->brands->update($id, ['is_active' => ! $brand->is_active]);

        return response()->json(['success' => true, 'is_active' => $brand->is_active]);
    }

    private function mapData(BrandRequest $request): array
    {
        $data = $request->safe()->only(['name']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        return $data;
    }
}
