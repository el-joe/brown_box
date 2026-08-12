<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\HasAudit;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class Product extends Model implements HasMedia
{
    use HasAudit, HasFactory, HasSlug, HasTranslations, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'category_id',
        'brand_id',
        'name',
        'slug',
        'description',
        'short_description',
        'sku',
        'barcode',
        'price',
        'compare_price',
        'cost_price',
        'has_variants',
        'is_active',
        'is_featured',
        'is_digital',
        'weight',
        'width',
        'height',
        'length',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'has_variants' => 'boolean',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_digital' => 'boolean',
        'weight' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'length' => 'decimal:2',
    ];

    public array $translatable = [
        'name',
        'description',
        'short_description',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (Product $model) => $model->getTranslation('name', 'en'))
            ->saveSlugsTo('slug');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function flashSaleItems(): HasMany
    {
        return $this->hasMany(FlashSaleItem::class);
    }

    public function coupons(): BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'coupon_products');
    }

    public function seoPage(): MorphOne
    {
        return $this->morphOne(SeoPage::class, 'model', 'model_type', 'model_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOnFlashSale($query)
    {
        $now = now();

        return $query->whereHas('flashSaleItems', function ($q) use ($now) {
            $q->whereHas('flashSale', function ($fs) use ($now) {
                $fs->where('is_active', true)
                    ->where('starts_at', '<=', $now)
                    ->where('ends_at', '>=', $now);
            });
        });
    }

    public function scopeSearch($query, ?string $keyword)
    {
        if (! $keyword) {
            return $query;
        }

        $locale = app()->getLocale();

        return $query->where(function ($q) use ($keyword, $locale) {
            $q->where('sku', 'like', "%{$keyword}%")
                ->orWhereRaw("JSON_EXTRACT(name, '$.\"{$locale}\"') LIKE ?", ["%{$keyword}%"])
                ->orWhereRaw("JSON_EXTRACT(description, '$.\"{$locale}\"') LIKE ?", ["%{$keyword}%"]);
        });
    }

    public function getEffectivePriceAttribute(): float
    {
        $now = now();

        $flashItem = $this->flashSaleItems()
            ->whereNull('variant_id')
            ->whereHas('flashSale', function ($q) use ($now) {
                $q->where('is_active', true)
                    ->where('starts_at', '<=', $now)
                    ->where('ends_at', '>=', $now);
            })
            ->first();

        if (! $flashItem) {
            return (float) $this->price;
        }

        if ($flashItem->discount_type === 'percentage') {
            $discounted = $this->price - ($this->price * $flashItem->discount_value / 100);
        } else {
            $discounted = $this->price - $flashItem->discount_value;
        }

        return max(0, (float) $discounted);
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->getEffectivePriceAttribute() < (float) $this->price;
    }

    public function getMainImageAttribute(): ?ProductImage
    {
        return $this->productImages->firstWhere('is_primary', true)
            ?? $this->productImages->first();
    }

    public function getTotalStockAttribute(): int
    {
        return (int) $this->stocks->sum('qty');
    }
}
