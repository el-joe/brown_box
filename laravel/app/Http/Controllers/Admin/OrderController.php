<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Governorate;
use App\Models\Order;
use App\Models\Product;
use App\Models\RefundRequest;
use App\Models\ShippingCompany;
use App\Models\Warehouse;
use App\Services\InvoiceService;
use App\Services\NotificationService;
use App\Services\OrderService;
use App\Services\ShippingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class OrderController extends Controller
{
    public function __construct(
        private readonly OrderService $orders,
        private readonly InvoiceService $invoices,
        private readonly NotificationService $notifications,
    ) {
    }

    public function index(Request $request): View
    {
        return view('admin.orders.index', [
            'filters' => $request->only([
                'order_number', 'customer', 'status', 'payment_status', 'payment_gateway',
                'from', 'to', 'affiliate_id', 'has_refund',
            ]),
            'shippingCompanies' => ShippingCompany::query()->active()->pluck('name', 'id'),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Order::query()->with(['customer', 'shippingCompany', 'affiliate'])->withCount('items');

        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%'.$request->string('order_number')->toString().'%');
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

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->string('payment_status')->toString());
        }

        if ($request->filled('payment_gateway')) {
            $query->where('payment_gateway', $request->string('payment_gateway')->toString());
        }

        if ($request->filled('from')) {
            $query->whereDate('created_at', '>=', $request->date('from'));
        }

        if ($request->filled('to')) {
            $query->whereDate('created_at', '<=', $request->date('to'));
        }

        if ($request->filled('affiliate_id')) {
            $query->where('affiliate_id', $request->integer('affiliate_id'));
        }

        if ($request->filled('has_refund')) {
            $query->when($request->boolean('has_refund'), fn ($q) => $q->whereHas('refundRequests'))
                ->when(! $request->boolean('has_refund'), fn ($q) => $q->whereDoesntHave('refundRequests'));
        }

        return DataTables::eloquent($query)
            ->addColumn('order_number', fn (Order $order) => e($order->order_number))
            ->addColumn('customer', fn (Order $order) => e($order->customer?->name ?? __('Guest')))
            ->addColumn('date', fn (Order $order) => $order->created_at->format('Y-m-d H:i'))
            ->addColumn('items', fn (Order $order) => (int) $order->items_count)
            ->addColumn('total', fn (Order $order) => money_format((float) $order->total_amount))
            ->addColumn('payment_gateway', fn (Order $order) => view('admin.orders._gateway-badge', ['order' => $order])->render())
            ->addColumn('payment_status', fn (Order $order) => view('admin.orders._payment-badge', ['order' => $order])->render())
            ->addColumn('status', fn (Order $order) => view('admin.orders._status-badge', ['order' => $order])->render())
            ->addColumn('shipping_status', fn (Order $order) => e($order->shipping_status ?? '—'))
            ->addColumn('actions', fn (Order $order) => view('admin.orders._actions', ['order' => $order])->render())
            ->rawColumns(['payment_gateway', 'payment_status', 'status', 'actions'])
            ->toJson();
    }

    public function show(int $id): View
    {
        $order = Order::query()
            ->with([
                'customer', 'items.product', 'items.variant', 'coupon', 'shippingCompany',
                'affiliate', 'affiliateCommissions', 'statusHistories.admin', 'refundRequests',
            ])
            ->findOrFail($id);

        return view('admin.orders.show', [
            'order' => $order,
            'shippingCompanies' => ShippingCompany::query()->active()->pluck('name', 'id'),
        ]);
    }

    public function verifyPayment(Request $request, int $id): RedirectResponse
    {
        $this->orders->verifyPayment($id, auth('admin')->id(), $request->string('notes')->toString() ?: null);

        return back()->with('success', __('Payment verified. Order confirmed.'));
    }

    public function rejectPayment(Request $request, int $id): RedirectResponse
    {
        $request->validate(['notes' => ['required', 'string', 'max:1000']]);

        $this->orders->rejectPayment($id, auth('admin')->id(), $request->string('notes')->toString());

        return back()->with('success', __('Payment rejected. Customer notified.'));
    }

    public function assignShipping(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'shipping_company_id' => ['nullable', 'integer', 'exists:shipping_companies,id'],
            'tracking_number' => ['nullable', 'string', 'max:191'],
        ]);

        $this->orders->assignShipping($id, $data['shipping_company_id'] ?? null, $data['tracking_number'] ?? null, auth('admin')->id());

        return back()->with('success', __('Shipping details updated.'));
    }

    public function changeShippingStatus(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'shipping_status' => ['required', 'in:pending,picked_up,in_transit,delivered,returned'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->orders->changeShippingStatus($id, $data['shipping_status'], auth('admin')->id(), $data['notes'] ?? null);

        return back()->with('success', __('Shipping status updated.'));
    }

    public function changeStatus(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,processing,shipped,delivered,cancelled,refunded'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->orders->changeStatus($id, $data['status'], $data['notes'] ?? null, auth('admin')->id());

        return back()->with('success', __('Order status updated.'));
    }

    public function updateAdminNotes(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate(['admin_notes' => ['nullable', 'string', 'max:5000']]);

        $this->orders->updateAdminNotes($id, $data['admin_notes'] ?? '');

        return back()->with('success', __('Admin notes saved.'));
    }

    public function printInvoice(int $id)
    {
        $order = Order::query()->findOrFail($id);

        return $this->invoices->download($order);
    }

    public function sendInvoice(int $id): RedirectResponse
    {
        $order = Order::query()->findOrFail($id);

        if (! $order->invoice_path) {
            $this->invoices->generate($order);
            $order->refresh();
        }

        Mail::to($order->customer->email)->send(new \App\Mail\InvoiceMail($order));

        return back()->with('success', __('Invoice emailed to customer.'));
    }

    public function createRefund(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:255'],
            'details' => ['nullable', 'string', 'max:2000'],
            'refund_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $order = Order::query()->findOrFail($id);

        RefundRequest::query()->create([
            'order_id' => $order->id,
            'customer_id' => $order->customer_id,
            'reason' => $data['reason'],
            'details' => $data['details'] ?? null,
            'status' => 'pending',
            'refund_amount' => $data['refund_amount'],
        ]);

        return back()->with('success', __('Refund request created.'));
    }

    public function pos(): View
    {
        return view('admin.orders.pos', [
            'categories' => Category::query()->active()->roots()->get(),
            'shippingCompanies' => ShippingCompany::query()->active()->pluck('name', 'id'),
            'governorates' => Governorate::query()->with('cities')->get(),
            'warehouses' => Warehouse::query()->active()->pluck('name', 'id'),
        ]);
    }

    public function posSearchProducts(Request $request): JsonResponse
    {
        $keyword = $request->string('q')->toString();
        $categoryId = $request->integer('category_id') ?: null;

        $query = Product::query()->with(['variants' => fn ($q) => $q->active(), 'mainImage'])->active();

        if ($keyword) {
            $query->search($keyword);
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->limit(40)->get();

        return response()->json($products->map(fn (Product $product) => [
            'id' => $product->id,
            'name' => $product->getTranslation('name', app()->getLocale()),
            'sku' => $product->sku,
            'price' => (float) $product->effective_price,
            'image' => $product->main_image ? asset_url($product->main_image->path) : null,
            'has_variants' => (bool) $product->has_variants,
            'variants' => $product->variants->map(fn ($variant) => [
                'id' => $variant->id,
                'label' => $variant->sku,
                'price' => (float) ($variant->price ?? $product->price),
            ])->values(),
        ])->values());
    }

    public function posQuickAddCustomer(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:191'],
        ]);

        $customer = Customer::query()->create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'] ?? strtolower(str_replace(' ', '', $data['name'])).'-'.$data['phone'].'@guest.local',
            'password' => bcrypt(str()->random(16)),
            'is_active' => true,
        ]);

        return response()->json(['id' => $customer->id, 'name' => $customer->name, 'phone' => $customer->phone]);
    }

    public function posSearchCustomers(Request $request): JsonResponse
    {
        $keyword = $request->string('q')->toString();

        $customers = Customer::query()
            ->when($keyword, fn ($q) => $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('phone', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%"))
            ->limit(15)
            ->get(['id', 'name', 'phone', 'email']);

        return response()->json($customers);
    }

    public function posShippingCost(Request $request, ShippingService $shipping): JsonResponse
    {
        $data = $request->validate([
            'shipping_company_id' => ['required', 'integer', 'exists:shipping_companies,id'],
            'governorate_id' => ['nullable', 'integer'],
            'city_id' => ['nullable', 'integer'],
        ]);

        $cost = $shipping->calculateCost(
            $data['shipping_company_id'],
            $data['governorate_id'] ?? null,
            $data['city_id'] ?? null,
        );

        return response()->json(['cost' => $cost]);
    }

    public function posStore(Request $request): RedirectResponse|JsonResponse
    {
        $data = $request->validate([
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.variant_id' => ['nullable', 'integer'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'coupon_code' => ['nullable', 'string'],
            'shipping_company_id' => ['nullable', 'integer', 'exists:shipping_companies,id'],
            'shipping_amount' => ['nullable', 'numeric', 'min:0'],
            'payment_gateway' => ['required', 'string'],
        ]);

        $items = [];

        foreach ($data['items'] as $item) {
            $product = Product::query()->findOrFail($item['product_id']);

            $items[] = [
                'product_id' => $product->id,
                'variant_id' => $item['variant_id'] ?? null,
                'product_name' => $product->getTranslation('name', app()->getLocale()),
                'product_sku' => $product->sku,
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
            ];
        }

        $order = $this->orders->createFromPOS([
            'customer_id' => $data['customer_id'],
            'warehouse_id' => $data['warehouse_id'],
            'items' => $items,
            'coupon_code' => $data['coupon_code'] ?? null,
            'shipping_company_id' => $data['shipping_company_id'] ?? null,
            'shipping_amount' => $data['shipping_amount'] ?? 0,
            'payment_gateway' => $data['payment_gateway'],
            'payment_status' => 'paid',
            'admin_id' => auth('admin')->id(),
        ]);

        return response()->json(['id' => $order->id, 'order_number' => $order->order_number]);
    }
}
