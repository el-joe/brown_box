<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['type', 'model_type', 'date_from', 'date_to', 'min_amount', 'max_amount']);

        $query = Transaction::query();

        if ($type = $filters['type'] ?? null) {
            $query->where('type', $type);
        }

        if ($modelType = $filters['model_type'] ?? null) {
            $query->where('model_type', $modelType);
        }

        if ($from = $filters['date_from'] ?? null) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $filters['date_to'] ?? null) {
            $query->whereDate('created_at', '<=', $to);
        }

        if ($min = $filters['min_amount'] ?? null) {
            $query->where('amount', '>=', $min);
        }

        if ($max = $filters['max_amount'] ?? null) {
            $query->where('amount', '<=', $max);
        }

        $transactions = $query->latest()->paginate(25)->withQueryString();

        $modelTypes = Transaction::query()->select('model_type')->distinct()->pluck('model_type');

        return view('admin.transactions.index', [
            'transactions' => $transactions,
            'filters' => $filters,
            'modelTypes' => $modelTypes->mapWithKeys(fn ($type) => [$type => class_basename($type)]),
        ]);
    }
}
