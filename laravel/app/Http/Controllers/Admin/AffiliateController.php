<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CommissionTierType;
use App\Enums\CommissionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AffiliateRequest;
use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\Category;
use App\Models\Customer;
use App\Models\PayoutRequest;
use App\Services\AffiliateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Yajra\DataTables\Facades\DataTables;

class AffiliateController extends Controller
{
    public function __construct(private readonly AffiliateService $affiliates) {}

    public function index(Request $request): View
    {
        return view('admin.affiliates.index', [
            'filters' => $request->only(['name', 'email', 'code', 'commission_type', 'is_active']),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $query = Affiliate::query()
            ->with('customer')
            ->withCount('orders');

        if ($name = $request->string('name')->toString()) {
            $query->whereHas('customer', fn ($q) => $q->where('name', 'like', "%{$name}%"));
        }

        if ($email = $request->string('email')->toString()) {
            $query->whereHas('customer', fn ($q) => $q->where('email', 'like', "%{$email}%"));
        }

        if ($code = $request->string('code')->toString()) {
            $query->where('code', 'like', "%{$code}%");
        }

        if ($request->filled('commission_type')) {
            $query->where('commission_type', $request->string('commission_type')->toString());
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        return DataTables::eloquent($query)
            ->addColumn('name', fn (Affiliate $affiliate) => e($affiliate->customer?->name ?? '—'))
            ->addColumn('email', fn (Affiliate $affiliate) => e($affiliate->customer?->email ?? '—'))
            ->addColumn('code', fn (Affiliate $affiliate) => e($affiliate->code))
            ->addColumn('commission_type', fn (Affiliate $affiliate) => view('admin.affiliates._commission_type', ['affiliate' => $affiliate])->render())
            ->addColumn('balance', fn (Affiliate $affiliate) => money_format((float) $affiliate->balance))
            ->addColumn('total_earned', fn (Affiliate $affiliate) => money_format((float) $affiliate->total_earned))
            ->addColumn('orders_count', fn (Affiliate $affiliate) => (int) $affiliate->orders_count)
            ->addColumn('status', fn (Affiliate $affiliate) => view('admin.affiliates._status', ['affiliate' => $affiliate])->render())
            ->addColumn('actions', fn (Affiliate $affiliate) => view('admin.affiliates._actions', ['affiliate' => $affiliate])->render())
            ->rawColumns(['commission_type', 'status', 'actions'])
            ->toJson();
    }

    public function create(): View
    {
        return view('admin.affiliates.form', $this->formData(new Affiliate));
    }

    public function store(AffiliateRequest $request): RedirectResponse
    {
        $affiliate = DB::transaction(function () use ($request) {
            $userId = $request->input('user_id') ?: $this->createCustomer($request->input('new_customer', []));

            $affiliate = Affiliate::query()->create($this->mapData($request, $userId));

            $this->syncCategoryCommissions($affiliate, $request->input('categories', []));

            if ($request->filled('opening_balance')) {
                $affiliate->update([
                    'balance' => (float) $request->input('opening_balance'),
                    'total_earned' => (float) $request->input('opening_balance'),
                ]);
            }

            return $affiliate;
        });

        return redirect()->route('admin.affiliates.index')->with('success', __('Affiliate created successfully.'));
    }

    public function edit(int $id): View
    {
        $affiliate = Affiliate::query()->with(['customer', 'categoryCommissions.tiers'])->findOrFail($id);

        return view('admin.affiliates.form', $this->formData($affiliate));
    }

    public function update(AffiliateRequest $request, int $id): RedirectResponse
    {
        $affiliate = Affiliate::query()->findOrFail($id);

        DB::transaction(function () use ($request, $affiliate) {
            $userId = $request->input('user_id') ?: $affiliate->user_id;

            $affiliate->update($this->mapData($request, $userId));

            $this->syncCategoryCommissions($affiliate, $request->input('categories', []));
        });

        return redirect()->route('admin.affiliates.index')->with('success', __('Affiliate updated successfully.'));
    }

    public function show(int $id): View
    {
        $affiliate = Affiliate::query()->with('customer')->findOrFail($id);

        return view('admin.affiliates.show', [
            'affiliate' => $affiliate,
            'pendingCommissions' => $affiliate->commissions()->where('status', 'pending')->sum('amount'),
        ]);
    }

    public function commissionsData(Request $request, int $id): JsonResponse
    {
        $query = AffiliateCommission::query()->where('affiliate_id', $id)->with('order');

        return DataTables::eloquent($query)
            ->addColumn('order_number', fn (AffiliateCommission $commission) => $commission->order?->order_number ?? __('Manual'))
            ->addColumn('order_total', fn (AffiliateCommission $commission) => $commission->order ? money_format((float) $commission->order->total_amount) : '—')
            ->addColumn('amount', fn (AffiliateCommission $commission) => money_format((float) $commission->amount))
            ->addColumn('status', fn (AffiliateCommission $commission) => view('admin.affiliates._commission_status', ['commission' => $commission])->render())
            ->addColumn('available_at', fn (AffiliateCommission $commission) => optional($commission->available_at)->format('Y-m-d H:i') ?? '—')
            ->addColumn('paid_at', fn (AffiliateCommission $commission) => optional($commission->paid_at)->format('Y-m-d H:i') ?? '—')
            ->rawColumns(['status'])
            ->toJson();
    }

    public function addManualCommission(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $this->affiliates->addManualCommission($id, (float) $data['amount'], $data['notes'] ?? null);

        return back()->with('success', __('Manual commission added.'));
    }

    public function payouts(Request $request): View
    {
        return view('admin.affiliates.payouts', [
            'filters' => $request->only(['affiliate', 'status']),
        ]);
    }

    public function payoutsData(Request $request): JsonResponse
    {
        $query = PayoutRequest::query()->with('affiliate.customer');

        if ($affiliate = $request->string('affiliate')->toString()) {
            $query->whereHas('affiliate.customer', fn ($q) => $q->where('name', 'like', "%{$affiliate}%"));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }

        return DataTables::eloquent($query)
            ->addColumn('affiliate', fn (PayoutRequest $payout) => e($payout->affiliate?->customer?->name ?? '—'))
            ->addColumn('amount', fn (PayoutRequest $payout) => money_format((float) $payout->amount))
            ->addColumn('method', fn (PayoutRequest $payout) => e($payout->payment_method ?? '—'))
            ->addColumn('details', fn (PayoutRequest $payout) => e(collect($payout->payment_details ?? [])->map(fn ($v, $k) => "{$k}: {$v}")->implode(', ') ?: '—'))
            ->addColumn('status', fn (PayoutRequest $payout) => view('admin.affiliates._payout_status', ['payout' => $payout])->render())
            ->addColumn('requested_at', fn (PayoutRequest $payout) => $payout->created_at->format('Y-m-d H:i'))
            ->addColumn('processed_at', fn (PayoutRequest $payout) => optional($payout->processed_at)->format('Y-m-d H:i') ?? '—')
            ->addColumn('actions', fn (PayoutRequest $payout) => view('admin.affiliates._payout_actions', ['payout' => $payout])->render())
            ->rawColumns(['status', 'actions'])
            ->toJson();
    }

    public function markPayoutPaid(int $id): RedirectResponse
    {
        $this->affiliates->markPayoutAsPaid($id);

        return back()->with('success', __('Payout marked as paid.'));
    }

    public function rejectPayout(Request $request, int $id): RedirectResponse
    {
        $data = $request->validate(['notes' => ['nullable', 'string', 'max:500']]);

        $this->affiliates->rejectPayout($id, $data['notes'] ?? null);

        return back()->with('success', __('Payout request rejected.'));
    }

    public function toggleActive(int $id): JsonResponse
    {
        $affiliate = Affiliate::query()->findOrFail($id);
        $affiliate->update(['is_active' => ! $affiliate->is_active]);

        return response()->json(['success' => true, 'is_active' => $affiliate->is_active]);
    }

    public function generateCode(): JsonResponse
    {
        return response()->json(['code' => 'AFF-'.strtoupper(Str::random(8))]);
    }

    public function searchCustomers(Request $request): JsonResponse
    {
        $keyword = $request->string('q')->toString();

        $customers = Customer::query()
            ->whereDoesntHave('affiliate')
            ->when($keyword, fn ($q) => $q->where('name', 'like', "%{$keyword}%")
                ->orWhere('email', 'like', "%{$keyword}%"))
            ->limit(15)
            ->get(['id', 'name', 'email']);

        return response()->json($customers);
    }

    public function validateAffiliate(AffiliateRequest $request): JsonResponse
    {
        return response()->json(['errors' => (object) []]);
    }

    private function createCustomer(array $data): int
    {
        return Customer::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => bcrypt(str()->random(16)),
            'is_active' => true,
        ])->id;
    }

    private function mapData(AffiliateRequest $request, int $userId): array
    {
        return [
            'user_id' => $userId,
            'code' => strtoupper($request->input('code')),
            'commission_type' => $request->input('commission_type'),
            'fixed_commission_rate' => $request->input('commission_type') === CommissionType::FixedAllOrders->value
                ? $request->input('fixed_commission_rate')
                : null,
            'is_active' => $request->boolean('is_active'),
            'approved_at' => $request->input('approved_at'),
        ];
    }

    private function syncCategoryCommissions(Affiliate $affiliate, array $categories): void
    {
        $affiliate->categoryCommissions()->each(fn ($commission) => $commission->tiers()->delete());
        $affiliate->categoryCommissions()->delete();

        foreach ($categories as $row) {
            $categoryCommission = $affiliate->categoryCommissions()->create([
                'category_id' => $row['category_id'],
                'tier_type' => $row['tier_type'],
                'rate' => $row['tier_type'] === CommissionTierType::FixedPercentage->value ? ($row['rate'] ?? 0) : 0,
            ]);

            if ($row['tier_type'] === CommissionTierType::Tiered->value) {
                foreach ($row['tiers'] ?? [] as $tier) {
                    $categoryCommission->tiers()->create([
                        'affiliate_id' => $affiliate->id,
                        'min_amount' => $tier['min_amount'],
                        'max_amount' => $tier['max_amount'] ?? null,
                        'rate' => $tier['rate'],
                    ]);
                }
            }
        }
    }

    private function formData(Affiliate $affiliate): array
    {
        return [
            'affiliate' => $affiliate,
            'categories' => Category::query()->active()->get()->mapWithKeys(
                fn (Category $category) => [$category->id => $category->getTranslation('name', app()->getLocale())]
            ),
            'customers' => Customer::query()
                ->where(fn ($q) => $q->whereDoesntHave('affiliate')->when($affiliate->user_id, fn ($q) => $q->orWhere('id', $affiliate->user_id)))
                ->orderBy('name')
                ->get(['id', 'name', 'email'])
                ->mapWithKeys(fn (Customer $customer) => [$customer->id => "{$customer->name} ({$customer->email})"]),
            'commissionTypes' => CommissionType::cases(),
            'tierTypes' => CommissionTierType::cases(),
            'existingCategories' => $affiliate->exists
                ? $affiliate->categoryCommissions->map(fn ($cc) => [
                    'category_id' => $cc->category_id,
                    'tier_type' => $cc->tier_type,
                    'rate' => (float) $cc->rate,
                    'tiers' => $cc->tiers->map(fn ($t) => [
                        'min_amount' => (float) $t->min_amount,
                        'max_amount' => $t->max_amount !== null ? (float) $t->max_amount : null,
                        'rate' => (float) $t->rate,
                    ])->values(),
                ])->values()
                : collect(),
        ];
    }
}
