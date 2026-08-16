<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupplierRequest;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    public function __construct(private readonly SupplierService $suppliers)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.suppliers.index', [
            'filters' => $request->only(['name']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Supplier::query()->withCount('purchases');

        if ($name = $request->string('name')->toString()) {
            $query->where('name', 'like', "%{$name}%");
        }

        return DataTables::eloquent($query)
            ->addColumn('name', fn (Supplier $supplier) => e($supplier->name))
            ->addColumn('phone', fn (Supplier $supplier) => e($supplier->phone ?? '—'))
            ->addColumn('email', fn (Supplier $supplier) => e($supplier->email ?? '—'))
            ->addColumn('balance', fn (Supplier $supplier) => number_format((float) $supplier->balance, 2))
            ->addColumn('purchases_count', fn (Supplier $supplier) => (string) $supplier->purchases_count)
            ->addColumn('actions', fn (Supplier $supplier) => view('admin.suppliers._actions', ['supplier' => $supplier])->render())
            ->rawColumns(['actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.suppliers.form', ['supplier' => new Supplier()]);
    }

    public function store(SupplierRequest $request): RedirectResponse
    {
        $this->suppliers->create($request->safe()->only(['name', 'phone', 'email', 'address', 'balance']));

        return redirect()->route('admin.suppliers.index')->with('success', __('Supplier created successfully.'));
    }

    public function edit(int $id): View
    {
        $supplier = $this->suppliers->find($id);

        abort_if(! $supplier, 404);

        return view('admin.suppliers.form', ['supplier' => $supplier]);
    }

    public function update(SupplierRequest $request, int $id): RedirectResponse
    {
        $this->suppliers->update($id, $request->safe()->only(['name', 'phone', 'email', 'address', 'balance']));

        return redirect()->route('admin.suppliers.index')->with('success', __('Supplier updated successfully.'));
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->suppliers->delete($id);

        return redirect()->route('admin.suppliers.index')->with('success', __('Supplier deleted successfully.'));
    }

    public function validateSupplier(SupplierRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }
}
