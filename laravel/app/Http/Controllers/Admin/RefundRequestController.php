<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RefundStatus;
use App\Http\Controllers\Controller;
use App\Models\RefundRequest;
use App\Services\RefundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class RefundRequestController extends Controller
{
    public function __construct(private readonly RefundService $refunds)
    {
    }

    public function index(Request $request): View
    {
        return view('admin.refunds.index', [
            'filters' => $request->only(['order_number', 'customer', 'status', 'from', 'to']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = RefundRequest::query()->with(['order', 'customer']);

        if ($request->filled('order_number')) {
            $term = $request->string('order_number')->toString();
            $query->whereHas('order', fn ($q) => $q->where('order_number', 'like', "%{$term}%"));
        }

        if ($request->filled('customer')) {
            $term = $request->string('customer')->toString();
            $query->whereHas('customer', function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        return DataTables::eloquent($query)
            ->addColumn('order_number', fn (RefundRequest $refund) => e($refund->order?->order_number ?? '—'))
            ->addColumn('customer', fn (RefundRequest $refund) => e($refund->customer?->name ?? '—'))
            ->addColumn('amount', fn (RefundRequest $refund) => money_format((float) ($refund->refund_amount ?? 0)))
            ->addColumn('reason', fn (RefundRequest $refund) => e(str($refund->reason)->limit(60)))
            ->addColumn('status', fn (RefundRequest $refund) => view('admin.refunds._status-badge', ['refund' => $refund])->render())
            ->addColumn('submitted_at', fn (RefundRequest $refund) => $refund->created_at->format('Y-m-d H:i'))
            ->addColumn('processed_at', fn (RefundRequest $refund) => $refund->processed_at?->format('Y-m-d H:i') ?? '—')
            ->addColumn('actions', fn (RefundRequest $refund) => view('admin.refunds._actions', ['refund' => $refund])->render())
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }

    public function show(int $id): View
    {
        $refund = RefundRequest::query()
            ->with(['order.items', 'order.customer', 'customer'])
            ->findOrFail($id);

        return view('admin.refunds.show', [
            'refund' => $refund,
        ]);
    }

    public function approve(Request $request, int $id): RedirectResponse
    {
        $refund = RefundRequest::query()->findOrFail($id);

        $data = $request->validate([
            'refund_amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($refund->status !== RefundStatus::Pending) {
            return back()->with('error', __('This refund request has already been processed.'));
        }

        $this->refunds->approve($refund, (float) $data['refund_amount'], $data['notes'] ?? null, auth('admin')->id());

        return redirect()->route('admin.refunds.show', $id)->with('success', __('Refund approved and processed.'));
    }

    public function reject(Request $request, int $id): RedirectResponse
    {
        $refund = RefundRequest::query()->findOrFail($id);

        $data = $request->validate([
            'notes' => ['required', 'string', 'max:2000'],
        ]);

        if ($refund->status !== RefundStatus::Pending) {
            return back()->with('error', __('This refund request has already been processed.'));
        }

        $this->refunds->reject($refund, $data['notes'], auth('admin')->id());

        return redirect()->route('admin.refunds.show', $id)->with('success', __('Refund request rejected.'));
    }
}
