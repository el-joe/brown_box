<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

class SeoPage extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'model_type',
        'model_id',
        'page_key',
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
        'robots',
        'schema_json',
    ];

    protected $casts = [
        'schema_json' => 'array',
    ];

    public array $translatable = [
        'title',
        'description',
        'keywords',
        'og_title',
        'og_description',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'model_type', 'model_id');
    }
}
