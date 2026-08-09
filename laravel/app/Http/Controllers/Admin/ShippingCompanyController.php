<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ShippingCompanyRequest;
use App\Models\City;
use App\Models\Governorate;
use App\Models\ShippingCompany;
use App\Services\ShippingCompanyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class ShippingCompanyController extends Controller
{
    public function __construct(private readonly ShippingCompanyService $shippingCompanies)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.shipping.index', [
            'filters' => $request->only(['name', 'is_active']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = ShippingCompany::query()->withCount('rates');

        if ($name = $request->string('name')->toString()) {
            $query->where('name', 'like', "%{$name}%");
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return DataTables::eloquent($query)
            ->addColumn('logo', fn (ShippingCompany $company) => $company->logo
                ? '<img src="'.e(asset_url($company->logo)).'" class="w-10 h-10 rounded-lg object-cover border border-slate-200">'
                : '<div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-300"><i class="fa-solid fa-truck"></i></div>')
            ->addColumn('name', fn (ShippingCompany $company) => e($company->name))
            ->addColumn('rates_count', fn (ShippingCompany $company) => (int) $company->rates_count)
            ->addColumn('status', fn (ShippingCompany $company) => view('admin.shipping._status', ['company' => $company])->render())
            ->addColumn('actions', fn (ShippingCompany $company) => view('admin.shipping._actions', ['company' => $company])->render())
            ->rawColumns(['logo', 'status', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.shipping.form', $this->formData(new ShippingCompany()));
    }

    public function store(ShippingCompanyRequest $request): RedirectResponse
    {
        $this->shippingCompanies->create($this->mapData($request));

        return redirect()->route('admin.shipping.index')->with('success', __('Shipping company created successfully.'));
    }

    public function edit(int $id): View
    {
        $company = $this->shippingCompanies->find($id);

        abort_if(! $company, 404);

        return view('admin.shipping.form', $this->formData($company));
    }

    public function update(ShippingCompanyRequest $request, int $id): RedirectResponse
    {
        $this->shippingCompanies->update($id, $this->mapData($request));

        return redirect()->route('admin.shipping.index')->with('success', __('Shipping company updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->shippingCompanies->delete($id);

        return redirect()->route('admin.shipping.index')->with('success', __('Shipping company deleted successfully.'));
    }

    public function validateShippingCompany(ShippingCompanyRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $company = $this->shippingCompanies->find($id);

        abort_if(! $company, 404);

        $company = $this->shippingCompanies->update($id, ['is_active' => ! $company->is_active]);

        return response()->json(['success' => true, 'is_active' => $company->is_active]);
    }

    public function citiesByGovernorate(int $governorate): JsonResponse
    {
        $cities = City::query()->where('governorate_id', $governorate)->get(['id', 'name_en', 'name_ar']);

        return response()->json($cities->mapWithKeys(fn (City $city) => [
            $city->id => $city->{'name_'.app()->getLocale()} ?? $city->name_en,
        ]));
    }

    private function mapData(ShippingCompanyRequest $request): array
    {
        $data = $request->safe()->only(['name', 'rates']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('shipping', 'public');
        }

        return $data;
    }

    private function formData(ShippingCompany $company): array
    {
        return [
            'company' => $company,
            'rates' => $company->exists
                ? $company->rates()->with('governorate', 'city')->get()->map(fn ($rate) => [
                    'id' => $rate->id,
                    'governorate_id' => $rate->governorate_id,
                    'city_id' => $rate->city_id,
                    'price' => $rate->price,
                    'estimated_days' => $rate->estimated_days,
                ])->values()
                : collect(),
            'governorates' => Governorate::query()->get()->mapWithKeys(fn (Governorate $governorate) => [
                $governorate->id => $governorate->{'name_'.app()->getLocale()} ?? $governorate->name_en,
            ]),
        ];
    }
}
