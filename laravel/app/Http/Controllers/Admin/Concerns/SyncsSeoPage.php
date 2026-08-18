<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Models\SeoPage;
use Illuminate\Database\Eloquent\Model;

trait SyncsSeoPage
{
    private function syncSeo(Model $model, array $seo): void
    {
        if (empty(array_filter($seo))) {
            return;
        }

        $data = [
            'title' => $seo['title'] ?? null,
            'description' => $seo['description'] ?? null,
            'keywords' => $seo['keywords'] ?? null,
            'og_title' => $seo['og_title'] ?? null,
            'og_description' => $seo['og_description'] ?? null,
            'canonical_url' => $seo['canonical_url'] ?? null,
            'robots' => $seo['robots'] ?? 'index,follow',
            'schema_json' => filled($seo['schema_json'] ?? null)
                ? json_decode($seo['schema_json'], true)
                : null,
        ];

        if (request()->hasFile('seo.og_image')) {
            $data['og_image'] = request()->file('seo.og_image')->store('seo', 'public');
        }

        SeoPage::query()->updateOrCreate(
            ['model_type' => $model::class, 'model_id' => $model->id],
            $data
        );
    }
}
