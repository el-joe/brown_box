<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Translatable\HasTranslations;

class BlogPost extends Model
{
    use HasFactory, HasSlug, HasTranslations, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'thumbnail',
        'author_id',
        'blog_category_id',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public array $translatable = [
        'title',
        'content',
        'excerpt',
    ];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom(fn (BlogPost $model) => $model->getTranslation('title', 'en'))
            ->saveSlugsTo('slug');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function seoPage(): MorphOne
    {
        return $this->morphOne(SeoPage::class, 'model', 'model_type', 'model_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)->where('published_at', '<=', now());
    }

    /**
     * String label for the post's category, used by the website views.
     * Defined as an accessor (checked before relations by Eloquent) so
     * `$post->category` renders as text while `category()` above still
     * provides the Eloquent relation for eager loading / queries.
     */
    public function getCategoryAttribute(): ?string
    {
        return $this->getRelationValue('category')?->name;
    }

    public function getImageUrlAttribute(): string
    {
        return asset_url($this->thumbnail) ?? asset('images/placeholder.png');
    }

    public function getAuthorNameAttribute(): string
    {
        return $this->getRelationValue('author')?->name ?? __('website.blog_default_author');
    }

    public function getAuthorAvatarUrlAttribute(): string
    {
        return asset_url($this->getRelationValue('author')?->avatar) ?? asset('images/placeholder.png');
    }

    public function getReadTimeAttribute(): string
    {
        $words = str_word_count(strip_tags((string) $this->content));
        $minutes = max(1, (int) ceil($words / 200));

        return trans_choice('website.blog_read_time', $minutes, ['minutes' => $minutes]);
    }
}
