@php
    $routeName = match ($movement->reference_type) {
        \App\Models\Purchase::class => 'admin.purchases.edit',
        \App\Models\Order::class => 'admin.orders.edit',
        default => null,
    };
    $url = ($routeName && $movement->reference_id && \Illuminate\Support\Facades\Route::has($routeName))
        ? route($routeName, $movement->reference_id)
        : null;
@endphp

@if ($url)
    <a href="{{ $url }}" class="text-amber-600 hover:underline">
        {{ class_basename($movement->reference_type) }} #{{ $movement->reference_id }}
    </a>
@else
    <span class="text-slate-400">—</span>
@endif
