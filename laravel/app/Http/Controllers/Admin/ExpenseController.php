<?php

namespace App\Http\Controllers\Admin;

use App\Exports\ExpensesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ExpenseRequest;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Services\ExpenseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ExpenseController extends Controller
{
    public function __construct(private readonly ExpenseService $expenses)
    {
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['category_id', 'payment_method', 'date_from', 'date_to', 'min_amount', 'max_amount']);

        $query = $this->filteredQuery($filters);

        $expenses = $query->clone()->with(['category', 'admin'])->latest('date')->paginate(20)->withQueryString();
        $total = $query->clone()->sum('amount');

        return view('admin.expenses.index', [
            'expenses' => $expenses,
            'total' => $total,
            'filters' => $filters,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['category_id', 'payment_method', 'date_from', 'date_to', 'min_amount', 'max_amount']);

        return Excel::download(new ExpensesExport($filters), 'expenses-'.now()->format('Y-m-d-His').'.xlsx');
    }

    public function create(): View
    {
        return view('admin.expenses.create', [
            'expense' => new Expense(),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(ExpenseRequest $request): RedirectResponse
    {
        $this->expenses->create($request->validated(), auth('admin')->id());

        return redirect()->route('admin.expenses.index')->with('success', __('Expense saved successfully.'));
    }

    public function edit(Expense $expense): View
    {
        return view('admin.expenses.edit', [
            'expense' => $expense,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(ExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $this->expenses->update($expense, $request->validated());

        return redirect()->route('admin.expenses.index')->with('success', __('Expense updated successfully.'));
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $this->expenses->delete($expense);

        return redirect()->route('admin.expenses.index')->with('success', __('Expense deleted successfully.'));
    }

    private function filteredQuery(array $filters)
    {
        $query = Expense::query();

        if ($categoryId = $filters['category_id'] ?? null) {
            $query->where('category_id', $categoryId);
        }

        if ($paymentMethod = $filters['payment_method'] ?? null) {
            $query->where('payment_method', $paymentMethod);
        }

        if ($from = $filters['date_from'] ?? null) {
            $query->whereDate('date', '>=', $from);
        }

        if ($to = $filters['date_to'] ?? null) {
            $query->whereDate('date', '<=', $to);
        }

        if ($min = $filters['min_amount'] ?? null) {
            $query->where('amount', '>=', $min);
        }

        if ($max = $filters['max_amount'] ?? null) {
            $query->where('amount', '<=', $max);
        }

        return $query;
    }

    private function categoryOptions(): \Illuminate\Support\Collection
    {
        return ExpenseCategory::query()->get()->mapWithKeys(
            fn (ExpenseCategory $category) => [$category->id => $category->full_path]
        );
    }
}
