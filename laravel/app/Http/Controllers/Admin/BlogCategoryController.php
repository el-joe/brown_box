<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BlogCategoryRequest;
use App\Models\BlogCategory;
use App\Services\BlogCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogCategoryController extends Controller
{
    public function __construct(private readonly BlogCategoryService $blogCategories)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['is_active']);

        $categories = $this->blogCategories->all($filters)->loadCount('posts');

        return view('admin.blog-categories.index', [
            'categories' => $categories,
            'filters' => $request->only(['is_active']),
        ]);
    }

    public function create(): View
    {
        return view('admin.blog-categories.form', [
            'category' => new BlogCategory(),
        ]);
    }

    public function store(BlogCategoryRequest $request): RedirectResponse
    {
        $this->blogCategories->create($this->mapData($request));

        return redirect()->route('admin.blog-categories.index')->with('success', __('Blog category created successfully.'));
    }

    public function edit(int $id): View
    {
        $category = $this->blogCategories->find($id);

        abort_if(! $category, 404);

        return view('admin.blog-categories.form', [
            'category' => $category,
        ]);
    }

    public function update(BlogCategoryRequest $request, int $id): RedirectResponse
    {
        $this->blogCategories->update($id, $this->mapData($request));

        return redirect()->route('admin.blog-categories.index')->with('success', __('Blog category updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->blogCategories->delete($id);

        return redirect()->route('admin.blog-categories.index')->with('success', __('Blog category deleted successfully.'));
    }

    public function validateBlogCategory(BlogCategoryRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $category = $this->blogCategories->find($id);

        abort_if(! $category, 404);

        $category = $this->blogCategories->update($id, ['is_active' => ! $category->is_active]);

        return response()->json(['success' => true, 'is_active' => $category->is_active]);
    }

    private function mapData(BlogCategoryRequest $request): array
    {
        $data = $request->safe()->only(['name']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
