<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Banner extends Model
{
    use HasFactory, HasTranslations, SoftDeletes;

    protected $fillable = [
        'title',
        'image',
        'type',
        'url',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public array $translatable = ['title'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function link(?string $lang = null): string
    {
        $lang ??= current_lang();

        return match ($this->type) {
            'product' => route('web.products.show', ['lang' => $lang, 'slug' => $this->url]),
            'category' => route('web.categories.show', ['lang' => $lang, 'categorySlug' => $this->url]),
            default => $this->url ?: '#',
        };
    }
}
