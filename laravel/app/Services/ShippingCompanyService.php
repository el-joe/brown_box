<?php

namespace App\Services;

use App\Models\ShippingRate;
use App\Repositories\Contracts\ShippingCompanyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ShippingCompanyService
{
    public function __construct(private readonly ShippingCompanyRepositoryInterface $shippingCompanies)
    {
    }

    public function all(array $filters = []): Collection
    {
        return $this->shippingCompanies->all($filters);
    }

    public function find(int $id): ?Model
    {
        return $this->shippingCompanies->find($id);
    }

    public function create(array $data): Model
    {
        return DB::transaction(function () use ($data) {
            $rates = $data['rates'] ?? [];
            unset($data['rates']);

            $company = $this->shippingCompanies->create($data);

            $this->syncRates($company->id, $rates);

            return $company->refresh();
        });
    }

    public function update(int $id, array $data): Model
    {
        return DB::transaction(function () use ($id, $data) {
            $rates = $data['rates'] ?? [];
            unset($data['rates']);

            $company = $this->shippingCompanies->update($id, $data);

            $this->syncRates($company->id, $rates);

            return $company->refresh();
        });
    }

    public function delete(int $id): bool
    {
        return $this->shippingCompanies->delete($id);
    }

    private function syncRates(int $companyId, array $rates): void
    {
        ShippingRate::query()->where('shipping_company_id', $companyId)->delete();

        foreach ($rates as $rate) {
            ShippingRate::query()->create([
                'shipping_company_id' => $companyId,
                'governorate_id' => $rate['governorate_id'],
                'city_id' => $rate['city_id'] ?? null,
                'price' => $rate['price'],
                'estimated_days' => $rate['estimated_days'] ?? null,
            ]);
        }
    }
}
