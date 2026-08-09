<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        return view('admin.customers.index', [
            'filters' => $request->only(['name', 'email', 'phone', 'status', 'from', 'to']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Customer::query()->withCount('orders')->withSum('orders as total_spent', 'total_amount');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->string('name')->toString().'%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%'.$request->string('email')->toString().'%');
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', '%'.$request->string('phone')->toString().'%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->boolean('status'));
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        return DataTables::eloquent($query)
            ->addColumn('avatar', fn (Customer $customer) => view('admin.customers._avatar', ['customer' => $customer])->render())
            ->addColumn('name', fn (Customer $customer) => e($customer->name))
            ->addColumn('email', fn (Customer $customer) => e($customer->email))
            ->addColumn('phone', fn (Customer $customer) => e($customer->phone ?? '—'))
            ->addColumn('orders_count', fn (Customer $customer) => (int) $customer->orders_count)
            ->addColumn('total_spent', fn (Customer $customer) => money_format((float) ($customer->total_spent ?? 0)))
            ->addColumn('registered_at', fn (Customer $customer) => $customer->created_at->format('Y-m-d H:i'))
            ->addColumn('status', fn (Customer $customer) => view('admin.customers._status-badge', ['customer' => $customer])->render())
            ->addColumn('actions', fn (Customer $customer) => view('admin.customers._actions', ['customer' => $customer])->render())
            ->rawColumns(['avatar', 'status', 'actions'])
            ->toJson();
    }

    public function show(int $id): View
    {
        $customer = Customer::query()
            ->with(['addresses.governorate', 'addresses.city', 'affiliate'])
            ->withCount('orders')
            ->withSum('orders as total_spent', 'total_amount')
            ->findOrFail($id);

        return view('admin.customers.show', [
            'customer' => $customer,
        ]);
    }

    public function ordersData(Request $request, int $id): JsonResponse
    {
        $query = Customer::query()->findOrFail($id)->orders()->getQuery();

        return DataTables::eloquent($query)
            ->addColumn('order_number', fn ($order) => e($order->order_number))
            ->addColumn('date', fn ($order) => $order->created_at->format('Y-m-d H:i'))
            ->addColumn('total', fn ($order) => money_format((float) $order->total_amount))
            ->addColumn('status', fn ($order) => view('admin.orders._status-badge', ['order' => $order])->render())
            ->addColumn('actions', fn ($order) => '<a href="'.route('admin.orders.show', $order).'" class="text-slate-400 hover:text-amber-600"><i class="fa-solid fa-eye"></i></a>')
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $customer = Customer::query()->findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191', 'unique:customers,email,'.$customer->id],
            'phone' => ['nullable', 'string', 'max:30'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $customer->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', __('Customer profile updated.'));
    }

    public function toggleActive(int $id): JsonResponse
    {
        $customer = Customer::query()->findOrFail($id);
        $customer->update(['is_active' => ! $customer->is_active]);

        return response()->json(['success' => true, 'is_active' => $customer->is_active]);
    }
}
