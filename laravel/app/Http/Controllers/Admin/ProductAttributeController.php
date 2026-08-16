<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductAttributeRequest;
use App\Models\ProductAttribute;
use App\Services\ProductAttributeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ProductAttributeController extends Controller
{
    public function __construct(private readonly ProductAttributeService $attributes)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.attributes.index', [
            'filters' => $request->only(['name']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = ProductAttribute::query()->withCount('values');

        if ($name = $request->string('name')->toString()) {
            $query->where(function ($q) use ($name) {
                $q->whereRaw('LOWER(JSON_EXTRACT(name, "$.en")) LIKE ?', ['%'.mb_strtolower($name).'%'])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(name, "$.ar")) LIKE ?', ['%'.mb_strtolower($name).'%']);
            });
        }

        return DataTables::eloquent($query)
            ->addColumn('name_en', fn (ProductAttribute $attribute) => e($attribute->getTranslation('name', 'en')))
            ->addColumn('name_ar', fn (ProductAttribute $attribute) => e($attribute->getTranslation('name', 'ar')))
            ->addColumn('values_count', fn (ProductAttribute $attribute) => (int) $attribute->values_count)
            ->addColumn('actions', fn (ProductAttribute $attribute) => view('admin.attributes._actions', ['attribute' => $attribute])->render())
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.attributes.form', [
            'attribute' => new ProductAttribute(),
        ]);
    }

    public function store(ProductAttributeRequest $request): RedirectResponse
    {
        $this->attributes->create($this->mapData($request));

        return redirect()->route('admin.attributes.index')->with('success', __('Attribute created successfully.'));
    }

    public function edit(int $id): View
    {
        $attribute = $this->attributes->find($id);

        abort_if(! $attribute, 404);

        $attribute->load('values');

        return view('admin.attributes.form', [
            'attribute' => $attribute,
        ]);
    }

    public function update(ProductAttributeRequest $request, int $id): RedirectResponse
    {
        $this->attributes->update($id, $this->mapData($request));

        return redirect()->route('admin.attributes.index')->with('success', __('Attribute updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->attributes->delete($id);

        return redirect()->route('admin.attributes.index')->with('success', __('Attribute deleted successfully.'));
    }

    public function validateAttribute(ProductAttributeRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    private function mapData(ProductAttributeRequest $request): array
    {
        return $request->safe()->only(['name', 'values']);
    }
}
