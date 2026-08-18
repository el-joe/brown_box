<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingEntry;
use App\Models\OpeningBalance;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountingController extends Controller
{
    /**
     * Section => list of node definitions (key, label, accounting_entries.category value).
     */
    private const STRUCTURE = [
        'Revenue' => [
            'sales'    => 'Sales',
            'shipping' => 'Shipping Income',
            'taxes'    => 'Tax Collected',
        ],
        'Expenses' => [
            'purchases' => 'Purchases (Cost of Goods)',
            'expenses'  => 'Operating Expenses',
            'affiliate' => 'Affiliate Commissions',
            'refunds'   => 'Refunds Issued',
        ],
        'Discounts' => [
            'discounts' => 'Discounts Given',
        ],
        'Equity' => [
            'opening_balances' => 'Opening Balances',
            'owner'            => 'Owner Transactions',
        ],
    ];

    public function index(Request $request): View
    {
        $filters = $request->only(['date_from', 'date_to']);

        $tree = [];

        foreach (self::STRUCTURE as $section => $nodes) {
            $sectionNodes = [];

            foreach ($nodes as $key => $label) {
                $totals = $this->totalsFor($key, $filters);

                $sectionNodes[] = [
                    'key' => $key,
                    'label' => $label,
                    'debit' => $totals['debit'],
                    'credit' => $totals['credit'],
                    'balance' => $totals['credit'] - $totals['debit'],
                ];
            }

            $tree[$section] = $sectionNodes;
        }

        return view('admin.accounting.index', [
            'tree' => $tree,
            'filters' => $filters,
        ]);
    }

    public function show(string $node, Request $request): View
    {
        $labels = collect(self::STRUCTURE)->collapse();
        abort_unless($labels->has($node), 404);

        $filters = $request->only(['date_from', 'date_to']);

        if ($node === 'opening_balances') {
            $entries = $this->openingBalanceQuery($filters)->latest('date')->paginate(25)->withQueryString();
        } else {
            $entries = $this->entryQuery($node, $filters)->with('admin')->latest('date')->paginate(25)->withQueryString();
        }

        return view('admin.accounting.show', [
            'node' => $node,
            'label' => $labels->get($node),
            'entries' => $entries,
            'filters' => $filters,
            'isOpeningBalance' => $node === 'opening_balances',
        ]);
    }

    private function totalsFor(string $key, array $filters): array
    {
        if ($key === 'opening_balances') {
            $amount = (float) $this->openingBalanceQuery($filters)->sum('amount');

            return ['debit' => 0, 'credit' => $amount];
        }

        $query = $this->entryQuery($key, $filters);

        return [
            'debit' => (float) (clone $query)->where('type', 'debit')->sum('amount'),
            'credit' => (float) (clone $query)->where('type', 'credit')->sum('amount'),
        ];
    }

    private function entryQuery(string $key, array $filters)
    {
        $query = AccountingEntry::query();

        match ($key) {
            'refunds' => $query->where('category', 'sales')->where('reference_type', \App\Models\RefundRequest::class),
            default => $query->where('category', $key),
        };

        if ($from = $filters['date_from'] ?? null) {
            $query->whereDate('date', '>=', $from);
        }

        if ($to = $filters['date_to'] ?? null) {
            $query->whereDate('date', '<=', $to);
        }

        return $query;
    }

    private function openingBalanceQuery(array $filters)
    {
        $query = OpeningBalance::query();

        if ($from = $filters['date_from'] ?? null) {
            $query->whereDate('date', '>=', $from);
        }

        if ($to = $filters['date_to'] ?? null) {
            $query->whereDate('date', '<=', $to);
        }

        return $query;
    }
}
