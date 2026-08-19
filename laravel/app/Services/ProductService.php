<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAttributeValue;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\SeoPage;
use App\Models\Stock;
use App\Models\Tag;
use App\Models\Warehouse;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(private readonly ProductRepositoryInterface $products)
    {
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->products->paginate($filters, $perPage);
    }

    public function find(int $id): ?Model
    {
        return $this->products->find($id);
    }

    public function findBySlug(string $slug): ?Model
    {
        return $this->products->findBySlug($slug);
    }

    public function featured(int $limit = 12): Collection
    {
        return $this->products->featured($limit);
    }

    public function search(string $keyword, int $limit = 20): Collection
    {
        return $this->products->search($keyword, $limit);
    }

    public function create(array $data): Model
    {
        return DB::transaction(fn () => $this->products->create($data));
    }

    public function update(int $id, array $data): Model
    {
        return DB::transaction(fn () => $this->products->update($id, $data));
    }

    public function delete(int $id): bool
    {
        return $this->products->delete($id);
    }

    /**
     * Create a product together with its tags, images, variants, stock records and SEO page.
     */
    public function store(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            /** @var Product $product */
            $product = $this->products->create($this->productAttributes($data));

            $this->syncTags($product, $data['tags'] ?? []);
            $this->syncImages($product, $data);
            $this->syncVariants($product, (bool) ($data['has_variants'] ?? false), $data['variants'] ?? []);
            $this->initializeStock($product);
            $this->syncSeo($product, $data);

            return $product->fresh(['tags', 'productImages', 'variants.variantAttributes', 'stocks', 'seoPage']);
        });
    }

    /**
     * Update a product together with its tags, images, variants, stock records and SEO page.
     */
    public function updateProduct(int $id, array $data): Product
    {
        return DB::transaction(function () use ($id, $data) {
            /** @var Product $product */
            $product = $this->products->update($id, $this->productAttributes($data));

            $this->syncTags($product, $data['tags'] ?? []);
            $this->syncImages($product, $data);
            $this->syncVariants($product, (bool) ($data['has_variants'] ?? false), $data['variants'] ?? []);
            $this->initializeStock($product);
            $this->syncSeo($product, $data);

            return $product->fresh(['tags', 'productImages', 'variants.variantAttributes', 'stocks', 'seoPage']);
        });
    }

    private function productAttributes(array $data): array
    {
        return array_intersect_key($data, array_flip([
            'category_id', 'brand_id', 'name', 'description', 'short_description',
            'sku', 'barcode', 'price', 'compare_price', 'cost_price',
            'has_variants', 'is_active', 'is_featured', 'is_hero', 'is_digital',
            'weight', 'width', 'height', 'length',
            'meta_title', 'meta_description', 'meta_keywords',
        ]));
    }

    private function syncTags(Product $product, array $tags): void
    {
        $tagIds = collect($tags)
            ->filter()
            ->map(fn ($tag) => is_numeric($tag)
                ? (int) $tag
                : Tag::query()->firstOrCreate(['slug' => Str::slug($tag)], ['name' => $tag])->id)
            ->unique()
            ->values()
            ->all();

        $product->tags()->sync($tagIds);
    }

    private function syncImages(Product $product, array $data): void
    {
        $newFiles = array_values($data['images'] ?? []);
        $existingIds = $data['existing_image_ids'] ?? null;
        $videoUrl = $data['video_url'] ?? null;

        // Existing IDs weren't submitted at all (e.g. legacy caller) — leave images untouched.
        if ($existingIds === null && $newFiles === [] && $videoUrl === null) {
            return;
        }

        $keepIds = collect($existingIds ?? []);

        $product->productImages()
            ->where('type', 'image')
            ->whereNotIn('id', $keepIds->all() ?: [0])
            ->delete();

        $product->productImages()->where('type', 'video')->delete();

        $sortOrder = $keepIds->count();

        $primaryExistingId = $data['primary_existing_image_id'] ?? null;
        $primaryNewIndex = $data['primary_new_image_index'] ?? null;

        $keepIds->each(fn (int $id) => ProductImage::query()->where('id', $id)->update([
            'is_primary' => $primaryExistingId !== null && (int) $primaryExistingId === $id,
        ]));

        foreach ($newFiles as $index => $file) {
            if (! $file) {
                continue;
            }

            $path = $file->store('products', 'public');

            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => $path,
                'type' => 'image',
                'is_primary' => $primaryNewIndex !== null && (int) $primaryNewIndex === (int) $index,
                'sort_order' => $sortOrder++,
            ]);
        }

        if ($videoUrl) {
            ProductImage::query()->create([
                'product_id' => $product->id,
                'path' => $videoUrl,
                'type' => 'video',
                'is_primary' => false,
                'sort_order' => $sortOrder,
            ]);
        }
    }

    private function syncVariants(Product $product, bool $hasVariants, array $variants): void
    {
        foreach ($product->variants()->get() as $variant) {
            $variant->variantAttributes()->delete();
            $variant->delete();
        }

        if (! $hasVariants) {
            return;
        }

        foreach (array_values($variants) as $index => $variantData) {
            $image = null;

            if (! empty($variantData['image']) && is_object($variantData['image'])) {
                $image = $variantData['image']->store('products/variants', 'public');
            }

            $variant = ProductVariant::query()->create([
                'product_id' => $product->id,
                'sku' => $variantData['sku'] ?? null,
                'barcode' => $variantData['barcode'] ?? null,
                'price' => $variantData['price'] ?? 0,
                'compare_price' => $variantData['compare_price'] ?? null,
                'cost_price' => $variantData['cost_price'] ?? null,
                'image' => $image,
                'is_active' => (bool) ($variantData['is_active'] ?? true),
                'sort_order' => $index,
            ]);

            foreach ($variantData['attribute_values'] ?? [] as $valueId) {
                $attributeValue = ProductAttributeValue::query()->find($valueId);

                if (! $attributeValue) {
                    continue;
                }

                $variant->variantAttributes()->create([
                    'product_attribute_id' => $attributeValue->product_attribute_id,
                    'product_attribute_value_id' => $attributeValue->id,
                ]);
            }
        }
    }

    private function initializeStock(Product $product): void
    {
        $warehouses = Warehouse::query()->active()->get();

        if ($warehouses->isEmpty()) {
            return;
        }

        $variants = $product->has_variants ? $product->variants()->get() : collect([null]);
        $keepVariantIds = $variants->filter()->pluck('id')->all();

        foreach ($warehouses as $warehouse) {
            foreach ($variants as $variant) {
                Stock::query()->firstOrCreate([
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'warehouse_id' => $warehouse->id,
                ], [
                    'qty' => 0,
                    'min_qty_alert' => 0,
                ]);
            }
        }

        Stock::query()
            ->where('product_id', $product->id)
            ->when($product->has_variants, fn ($q) => $q->whereNotIn('variant_id', $keepVariantIds ?: [0]))
            ->when(! $product->has_variants, fn ($q) => $q->whereNotNull('variant_id'))
            ->delete();
    }

    private function syncSeo(Product $product, array $data): void
    {
        SeoPage::query()->updateOrCreate(
            ['model_type' => Product::class, 'model_id' => $product->id],
            [
                'page_key' => 'product-'.$product->id,
                'title' => $data['meta_title'] ?? $product->name,
                'description' => $data['meta_description'] ?? $product->short_description,
                'keywords' => $data['meta_keywords'] ?? null,
                'og_title' => $data['og_title'] ?? null,
                'og_description' => $data['og_description'] ?? null,
                'og_image' => $data['og_image'] ?? null,
                'canonical_url' => $data['canonical_url'] ?? null,
                'robots' => $data['robots'] ?? 'index,follow',
                'schema_json' => $data['schema_json'] ?? null,
            ]
        );
    }
}
