<?php

namespace App\Services;

use App\Models\ProductAttribute;
use App\Repositories\Contracts\ProductAttributeRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProductAttributeService
{
    public function __construct(private readonly ProductAttributeRepositoryInterface $attributes)
    {
    }

    public function all(array $filters = []): Collection
    {
        return $this->attributes->all($filters);
    }

    public function find(int $id): ?ProductAttribute
    {
        return $this->attributes->find($id);
    }

    public function create(array $data): ProductAttribute
    {
        return DB::transaction(function () use ($data) {
            $values = $data['values'] ?? [];
            unset($data['values']);

            /** @var ProductAttribute $attribute */
            $attribute = $this->attributes->create($data);

            $this->syncValues($attribute, $values);

            return $attribute->refresh();
        });
    }

    public function update(int $id, array $data): ProductAttribute
    {
        return DB::transaction(function () use ($id, $data) {
            $values = $data['values'] ?? [];
            unset($data['values']);

            /** @var ProductAttribute $attribute */
            $attribute = $this->attributes->update($id, $data);

            $this->syncValues($attribute, $values);

            return $attribute->refresh();
        });
    }

    public function delete(int $id): bool
    {
        return $this->attributes->delete($id);
    }

    private function syncValues(ProductAttribute $attribute, array $values): void
    {
        $keepIds = [];

        foreach ($values as $value) {
            $valueData = [
                'value' => $value['value'],
                'extra_price' => $value['extra_price'] ?? 0,
            ];

            if (! empty($value['id'])) {
                $attributeValue = $attribute->values()->find($value['id']);

                if ($attributeValue) {
                    $attributeValue->update($valueData);
                    $keepIds[] = $attributeValue->id;

                    continue;
                }
            }

            $keepIds[] = $attribute->values()->create($valueData)->id;
        }

        $attribute->values()->whereNotIn('id', $keepIds)->delete();
    }
}
