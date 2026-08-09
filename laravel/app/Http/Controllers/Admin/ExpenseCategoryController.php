<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExpenseCategoryRequest;
use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['name', 'parent_id', 'type', 'is_active']);

        $categories = ExpenseCategory::query()->with('parent')->get();

        if ($search = $request->string('name')->toString()) {
            $categories = $categories->filter(
                fn (ExpenseCategory $category) => str_contains(mb_strtolower($category->getTranslation('name', 'en') ?? ''), mb_strtolower($search))
                    || str_contains(mb_strtolower($category->getTranslation('name', 'ar') ?? ''), mb_strtolower($search))
            );
        }

        if ($request->filled('type')) {
            $categories = $categories->where('type', $request->string('type')->toString());
        }

        if ($request->filled('is_active')) {
            $categories = $categories->where('is_active', $request->boolean('is_active'));
        }

        $tree = $this->buildTree($categories);
        $parents = ExpenseCategory::query()->get();

        return view('admin.expense-categories.index', [
            'tree' => $tree,
            'parents' => $parents,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.expense-categories.form', [
            'category' => new ExpenseCategory(),
            'parents' => $this->availableParents(),
        ]);
    }

    public function store(ExpenseCategoryRequest $request): RedirectResponse
    {
        ExpenseCategory::query()->create($this->mapData($request));

        return redirect()->route('admin.expense-categories.index')->with('success', __('Expense category created successfully.'));
    }

    public function edit(ExpenseCategory $expense_category): View
    {
        return view('admin.expense-categories.form', [
            'category' => $expense_category,
            'parents' => $this->availableParents($expense_category),
        ]);
    }

    public function update(ExpenseCategoryRequest $request, ExpenseCategory $expense_category): RedirectResponse
    {
        $expense_category->update($this->mapData($request));

        return redirect()->route('admin.expense-categories.index')->with('success', __('Expense category updated successfully.'));
    }

    public function destroy(ExpenseCategory $expense_category): RedirectResponse
    {
        if ($expense_category->children()->exists() || $expense_category->expenses()->exists()) {
            return back()->withErrors(['category' => __('Cannot delete a category that has sub-categories or expenses.')]);
        }

        $expense_category->delete();

        return redirect()->route('admin.expense-categories.index')->with('success', __('Expense category deleted successfully.'));
    }

    public function validateCategory(ExpenseCategoryRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    public function toggleActive(ExpenseCategory $expense_category): JsonResponse
    {
        $expense_category->update(['is_active' => ! $expense_category->is_active]);

        return response()->json(['success' => true, 'is_active' => $expense_category->is_active]);
    }

    private function mapData(ExpenseCategoryRequest $request): array
    {
        $data = $request->safe()->only(['name', 'parent_id', 'type']);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function availableParents(?ExpenseCategory $category = null): Collection
    {
        $categories = ExpenseCategory::query()->get();

        if (! $category || ! $category->exists) {
            return $categories;
        }

        $excluded = collect([$category->id])->merge($this->descendantIds($category, $categories));

        return $categories->reject(fn (ExpenseCategory $item) => $excluded->contains($item->id));
    }

    private function descendantIds(ExpenseCategory $category, Collection $categories): array
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
            ->sortBy('id')
            ->map(function (ExpenseCategory $category) use ($categories) {
                $category->setRelation('childrenTree', $this->buildTree($categories, $category->id));

                return $category;
            })
            ->values();
    }
}
