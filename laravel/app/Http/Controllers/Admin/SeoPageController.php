<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SeoPageRequest;
use App\Services\SeoPageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeoPageController extends Controller
{
    public function __construct(private readonly SeoPageService $seo)
    {
    }

    public function index(): View
    {
        return view('admin.seo.index', [
            'staticPages' => $this->seo->staticPageGroups(),
            'products' => $this->seo->productGroup(),
            'categories' => $this->seo->categoryGroup(),
        ]);
    }

    public function editPage(string $pageKey): View
    {
        abort_unless($this->seo->isValidPageKey($pageKey), 404);

        return view('admin.seo.form', [
            'seoPage' => $this->seo->findOrNewForPage($pageKey),
            'title' => $this->seo->pageLabel($pageKey),
            'submitUrl' => route('admin.seo.page.update', $pageKey),
        ]);
    }

    public function updatePage(SeoPageRequest $request, string $pageKey): RedirectResponse
    {
        abort_unless($this->seo->isValidPageKey($pageKey), 404);

        $seoPage = $this->seo->findOrNewForPage($pageKey);
        $seoPage->page_key = $pageKey;
        $this->seo->save($seoPage, $this->mapData($request));

        return redirect()->route('admin.seo.index')->with('success', __('SEO settings updated successfully.'));
    }

    public function editModel(string $modelType, int $modelId): View
    {
        abort_unless($this->seo->isValidModelType($modelType), 404);

        $modelClass = $this->seo->modelClass($modelType);
        $model = $modelClass::query()->findOrFail($modelId);

        return view('admin.seo.form', [
            'seoPage' => $this->seo->findOrNewForModel($modelType, $modelId),
            'title' => $model->getTranslation('name', 'en') ?: $model->getTranslation('name', 'ar'),
            'submitUrl' => route('admin.seo.model.update', [$modelType, $modelId]),
        ]);
    }

    public function updateModel(SeoPageRequest $request, string $modelType, int $modelId): RedirectResponse
    {
        abort_unless($this->seo->isValidModelType($modelType), 404);

        $modelClass = $this->seo->modelClass($modelType);
        $modelClass::query()->findOrFail($modelId);

        $seoPage = $this->seo->findOrNewForModel($modelType, $modelId);
        $this->seo->save($seoPage, $this->mapData($request));

        return redirect()->route('admin.seo.index')->with('success', __('SEO settings updated successfully.'));
    }

    public function bulkUpdateProducts(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title.en' => ['nullable', 'string', 'max:255'],
            'title.ar' => ['nullable', 'string', 'max:255'],
            'description.en' => ['nullable', 'string', 'max:500'],
            'description.ar' => ['nullable', 'string', 'max:500'],
            'robots' => ['nullable', 'string'],
        ]);

        $count = $this->seo->bulkApplyToProductsMissingSeo($data);

        return redirect()->route('admin.seo.index')->with('success', __(':count product(s) updated.', ['count' => $count]));
    }

    private function mapData(SeoPageRequest $request): array
    {
        $data = $request->safe()->only([
            'title', 'description', 'keywords', 'og_title', 'og_description', 'canonical_url', 'robots',
        ]);

        if ($request->filled('schema_json')) {
            $data['schema_json'] = json_decode($request->string('schema_json')->toString(), true);
        }

        if ($request->hasFile('og_image')) {
            $data['og_image'] = $request->file('og_image')->store('seo', 'public');
        }

        return $data;
    }
}
