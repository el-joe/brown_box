<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\SyncsSeoPage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StaticPageRequest;
use App\Models\StaticPage;
use App\Services\StaticPageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StaticPageController extends Controller
{
    use SyncsSeoPage;

    public function __construct(private readonly StaticPageService $pages)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'is_active']);

        $pages = $this->pages->paginate($filters);

        return view('admin.static-pages.index', [
            'pages' => $pages,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.static-pages.form', [
            'page' => new StaticPage(),
        ]);
    }

    public function store(StaticPageRequest $request): RedirectResponse
    {
        $page = $this->pages->create($this->mapData($request));
        $this->syncSeo($page, $request->input('seo', []));

        return redirect()->route('admin.static-pages.index')->with('success', __('Static page created successfully.'));
    }

    public function edit(int $id): View
    {
        $page = $this->pages->find($id);

        abort_if(! $page, 404);

        $page->load('seoPage');

        return view('admin.static-pages.form', ['page' => $page]);
    }

    public function update(StaticPageRequest $request, int $id): RedirectResponse
    {
        $page = $this->pages->update($id, $this->mapData($request));
        $this->syncSeo($page, $request->input('seo', []));

        return redirect()->route('admin.static-pages.index')->with('success', __('Static page updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->pages->delete($id);

        return redirect()->route('admin.static-pages.index')->with('success', __('Static page deleted successfully.'));
    }

    public function validateStaticPage(StaticPageRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $page = $this->pages->find($id);

        abort_if(! $page, 404);

        $page = $this->pages->update($id, ['is_active' => ! $page->is_active]);

        return response()->json(['success' => true, 'is_active' => $page->is_active]);
    }

    private function mapData(StaticPageRequest $request): array
    {
        $data = $request->safe()->only(['title', 'content', 'slug']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
