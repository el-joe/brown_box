<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class ProductAttribute extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'name',
    ];

    public array $translatable = ['name'];

    public function values(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    public function variantAttributes(): HasMany
    {
        return $this->hasMany(ProductVariantAttribute::class);
    }
}
