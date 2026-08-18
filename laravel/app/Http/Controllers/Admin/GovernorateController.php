<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Governorate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GovernorateController extends Controller
{
    public function index(Request $request): View
    {
        $query = Governorate::query()->with('country')->withCount('cities');

        if ($name = $request->string('name')->toString()) {
            $query->where(function ($q) use ($name) {
                $q->where('name_en', 'like', "%{$name}%")
                    ->orWhere('name_ar', 'like', "%{$name}%");
            });
        }

        return view('admin.governorates.index', [
            'governorates' => $query->orderBy('name_en')->get(),
            'filters' => $request->only(['name']),
        ]);
    }

    public function create(): View
    {
        return view('admin.governorates.form', [
            'governorate' => new Governorate(),
            'countries' => Country::query()->orderBy('name_en')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Governorate::query()->create($this->validateGovernorate($request));

        return redirect()->route('admin.governorates.index')->with('success', __('Governorate created successfully.'));
    }

    public function edit(Governorate $governorate): View
    {
        return view('admin.governorates.form', [
            'governorate' => $governorate,
            'countries' => Country::query()->orderBy('name_en')->get(),
        ]);
    }

    public function update(Request $request, Governorate $governorate): RedirectResponse
    {
        $governorate->update($this->validateGovernorate($request));

        return redirect()->route('admin.governorates.index')->with('success', __('Governorate updated successfully.'));
    }

    public function destroy(Governorate $governorate): RedirectResponse
    {
        if ($governorate->cities()->exists() || $governorate->customerAddresses()->exists() || $governorate->shippingRates()->exists()) {
            return back()->withErrors(['governorate' => __('Cannot delete a governorate that has cities, addresses, or shipping rates linked to it.')]);
        }

        $governorate->delete();

        return redirect()->route('admin.governorates.index')->with('success', __('Governorate deleted successfully.'));
    }

    public function cities(Governorate $governorate): View
    {
        return view('admin.governorates.cities', [
            'governorate' => $governorate,
            'cities' => $governorate->cities()->orderBy('name_en')->get(),
        ]);
    }

    public function storeCity(Request $request, Governorate $governorate): RedirectResponse
    {
        $governorate->cities()->create($this->validateCity($request));

        return redirect()->route('admin.governorates.cities', $governorate)->with('success', __('City created successfully.'));
    }

    public function updateCity(Request $request, Governorate $governorate, City $city): RedirectResponse
    {
        abort_if($city->governorate_id !== $governorate->id, 404);

        $city->update($this->validateCity($request));

        return redirect()->route('admin.governorates.cities', $governorate)->with('success', __('City updated successfully.'));
    }

    public function destroyCity(Governorate $governorate, City $city): RedirectResponse
    {
        abort_if($city->governorate_id !== $governorate->id, 404);

        if ($city->warehouses()->exists() || $city->customerAddresses()->exists() || $city->shippingRates()->exists()) {
            return back()->withErrors(['city' => __('Cannot delete a city that has warehouses, addresses, or shipping rates linked to it.')]);
        }

        $city->delete();

        return redirect()->route('admin.governorates.cities', $governorate)->with('success', __('City deleted successfully.'));
    }

    private function validateGovernorate(Request $request): array
    {
        return $request->validate([
            'name_en' => ['required', 'string', 'max:100'],
            'name_ar' => ['required', 'string', 'max:100'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ]);
    }

    private function validateCity(Request $request): array
    {
        return $request->validate([
            'name_en' => ['required', 'string', 'max:100'],
            'name_ar' => ['required', 'string', 'max:100'],
        ]);
    }
}
