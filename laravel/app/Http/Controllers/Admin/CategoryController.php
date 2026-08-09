<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function __construct(private readonly CategoryService $categories)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['parent_id', 'is_active']);
        $search = $request->string('name')->toString();

        $categories = $this->categories->all($filters)->load('parent');

        if ($search !== '') {
            $categories = $categories->filter(
                fn (Category $category) => str_contains(mb_strtolower($category->getTranslation('name', 'en') ?? ''), mb_strtolower($search))
                    || str_contains(mb_strtolower($category->getTranslation('name', 'ar') ?? ''), mb_strtolower($search))
            );
        }

        $tree = $this->buildTree($categories);
        $parents = $this->categories->all()->whereNull('parent_id');

        return view('admin.categories.index', [
            'tree' => $tree,
            'parents' => $parents,
            'filters' => $request->only(['name', 'parent_id', 'is_active']),
        ]);
    }

    public function create(): View
    {
        return view('admin.categories.form', [
            'category' => new Category(),
            'parents' => $this->availableParents(),
        ]);
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $this->categories->create($this->mapData($request));

        return redirect()->route('admin.categories.index')->with('success', __('Category created successfully.'));
    }

    public function edit(int $id): View
    {
        $category = $this->categories->find($id);

        abort_if(! $category, 404);

        return view('admin.categories.form', [
            'category' => $category,
            'parents' => $this->availableParents($category),
        ]);
    }

    public function update(CategoryRequest $request, int $id): RedirectResponse
    {
        $this->categories->update($id, $this->mapData($request));

        return redirect()->route('admin.categories.index')->with('success', __('Category updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->categories->delete($id);

        return redirect()->route('admin.categories.index')->with('success', __('Category deleted successfully.'));
    }

    public function validateCategory(CategoryRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    public function toggleActive(int $id): JsonResponse
    {
        $category = $this->categories->find($id);

        abort_if(! $category, 404);

        $category = $this->categories->update($id, ['is_active' => ! $category->is_active]);

        return response()->json(['success' => true, 'is_active' => $category->is_active]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $items = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['required', 'integer', 'exists:categories,id'],
            'items.*.parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'items.*.sort_order' => ['required', 'integer'],
        ])['items'];

        foreach ($items as $item) {
            $this->categories->update($item['id'], [
                'parent_id' => $item['parent_id'] ?? null,
                'sort_order' => $item['sort_order'],
            ]);
        }

        return response()->json(['success' => true]);
    }

    private function mapData(CategoryRequest $request): array
    {
        $data = $request->safe()->only(['name', 'parent_id', 'icon', 'sort_order']);
        $data['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        return $data;
    }

    private function availableParents(?Category $category = null): Collection
    {
        $categories = $this->categories->all();

        if (! $category || ! $category->exists) {
            return $categories;
        }

        $excluded = collect([$category->id])->merge($this->descendantIds($category, $categories));

        return $categories->reject(fn (Category $item) => $excluded->contains($item->id));
    }

    private function descendantIds(Category $category, Collection $categories): array
    {
        $ids = [];

        foreach ($categories->where('parent_id', $category->id) as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->descendantIds($child, $categories));
        }

        return $ids;
    }

    private function buildTree(Collection $categories, ?int $parentId = null): Collection
    {
        return $categories
            ->where('parent_id', $parentId)
            ->sortBy('sort_order')
            ->map(function (Category $category) use ($categories) {
                $category->setRelation('childrenTree', $this->buildTree($categories, $category->id));

                return $category;
            })
            ->values();
    }
}
