<?php

namespace App\Services;

use App\Repositories\Contracts\ShippingRateRepositoryInterface;
use InvalidArgumentException;

class ShippingService
{
    public function __construct(private readonly ShippingRateRepositoryInterface $shippingRates)
    {
    }

    /**
     * Calculate the shipping cost for a given shipping company and location.
     * Falls back from city-level rate to governorate-level rate.
     */
    public function calculateCost(int $shippingCompanyId, ?int $governorateId, ?int $cityId): float
    {
        $rate = $this->shippingRates->findForLocation($shippingCompanyId, $governorateId, $cityId);

        if (! $rate) {
            throw new InvalidArgumentException('No shipping rate configured for the given location.');
        }

        return (float) $rate->price;
    }

    public function estimatedDays(int $shippingCompanyId, ?int $governorateId, ?int $cityId): ?int
    {
        $rate = $this->shippingRates->findForLocation($shippingCompanyId, $governorateId, $cityId);

        return $rate?->estimated_days;
    }
}
