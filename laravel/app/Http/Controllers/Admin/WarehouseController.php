<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\WarehouseRequest;
use App\Models\City;
use App\Models\Governorate;
use App\Models\Warehouse;
use App\Services\WarehouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class WarehouseController extends Controller
{
    public function __construct(private readonly WarehouseService $warehouses)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.warehouses.index', [
            'filters' => $request->only(['name', 'is_active']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Warehouse::query()->with('city.governorate')->withCount('stocks');

        if ($name = $request->string('name')->toString()) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return DataTables::eloquent($query)
            ->addColumn('name', fn (Warehouse $warehouse) => e($warehouse->name))
            ->addColumn('city', fn (Warehouse $warehouse) => e($warehouse->city?->name_en ?? '—'))
            ->addColumn('governorate', fn (Warehouse $warehouse) => e($warehouse->city?->governorate?->name_en ?? '—'))
            ->addColumn('is_default', fn (Warehouse $warehouse) => view('admin.warehouses._default', ['warehouse' => $warehouse])->render())
            ->addColumn('status', fn (Warehouse $warehouse) => view('admin.warehouses._status', ['warehouse' => $warehouse])->render())
            ->addColumn('actions', fn (Warehouse $warehouse) => view('admin.warehouses._actions', ['warehouse' => $warehouse])->render())
            ->rawColumns(['is_default', 'status', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.warehouses.form', $this->formData(new Warehouse()));
    }

    public function store(WarehouseRequest $request): RedirectResponse
    {
        $this->warehouses->create($this->mapData($request));

        return redirect()->route('admin.warehouses.index')->with('success', __('Warehouse created successfully.'));
    }

    public function edit(int $id): View
    {
        $warehouse = $this->warehouses->find($id);

        abort_if(! $warehouse, 404);

        return view('admin.warehouses.form', $this->formData($warehouse));
    }

    public function update(WarehouseRequest $request, int $id): RedirectResponse
    {
        $this->warehouses->update($id, $this->mapData($request));

        return redirect()->route('admin.warehouses.index')->with('success', __('Warehouse updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->warehouses->delete($id);

        return redirect()->route('admin.warehouses.index')->with('success', __('Warehouse deleted successfully.'));
    }

    public function validateWarehouse(WarehouseRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $warehouse = $this->warehouses->find($id);

        abort_if(! $warehouse, 404);

        $warehouse = $this->warehouses->update($id, ['is_active' => ! $warehouse->is_active]);

        return response()->json(['success' => true, 'is_active' => $warehouse->is_active]);
    }

    public function makeDefault(int $id): JsonResponse
    {
        $warehouse = $this->warehouses->find($id);

        abort_if(! $warehouse, 404);

        $this->warehouses->update($id, ['is_default' => true]);

        return response()->json(['success' => true]);
    }

    public function citiesByGovernorate(int $governorate): JsonResponse
    {
        $cities = City::query()->where('governorate_id', $governorate)->get(['id', 'name_en', 'name_ar']);

        return response()->json($cities->mapWithKeys(fn (City $city) => [
            $city->id => $city->{'name_'.app()->getLocale()} ?? $city->name_en,
        ]));
    }

    private function mapData(WarehouseRequest $request): array
    {
        $data = $request->safe()->only(['name', 'address', 'city_id']);
        $data['is_active'] = $request->boolean('is_active');
        $data['is_default'] = $request->boolean('is_default');

        return $data;
    }

    private function formData(Warehouse $warehouse): array
    {
        return [
            'warehouse' => $warehouse,
            'governorates' => Governorate::query()->get()->mapWithKeys(fn (Governorate $governorate) => [
                $governorate->id => $governorate->{'name_'.app()->getLocale()} ?? $governorate->name_en,
            ]),
            'cities' => $warehouse->city_id
                ? City::query()->where('governorate_id', $warehouse->city?->governorate_id)->get()->mapWithKeys(fn (City $city) => [
                    $city->id => $city->{'name_'.app()->getLocale()} ?? $city->name_en,
                ])
                : collect(),
        ];
    }
}
