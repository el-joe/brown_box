@props(['status'])

@php
    use App\Enums\OrderStatus;
    use App\Enums\PaymentStatus;
    use App\Enums\RefundStatus;

    $label = $status instanceof \BackedEnum ? ($status->label() ?? $status->value) : (string) $status;

    $colors = match (true) {
        $status instanceof OrderStatus => match ($status) {
            OrderStatus::Pending => 'bg-slate-100 text-slate-700',
            OrderStatus::Confirmed => 'bg-blue-100 text-blue-700',
            OrderStatus::Processing => 'bg-indigo-100 text-indigo-700',
            OrderStatus::Shipped => 'bg-purple-100 text-purple-700',
            OrderStatus::Delivered => 'bg-emerald-100 text-emerald-700',
            OrderStatus::Cancelled => 'bg-red-100 text-red-700',
            OrderStatus::Refunded => 'bg-amber-100 text-amber-700',
        },
        $status instanceof PaymentStatus => match ($status) {
            PaymentStatus::Unpaid => 'bg-slate-100 text-slate-700',
            PaymentStatus::PendingVerification => 'bg-amber-100 text-amber-700',
            PaymentStatus::Paid => 'bg-emerald-100 text-emerald-700',
            PaymentStatus::Failed => 'bg-red-100 text-red-700',
            PaymentStatus::Refunded => 'bg-purple-100 text-purple-700',
        },
        $status instanceof RefundStatus => match ($status) {
            RefundStatus::Pending => 'bg-amber-100 text-amber-700',
            RefundStatus::Approved => 'bg-blue-100 text-blue-700',
            RefundStatus::Processed => 'bg-emerald-100 text-emerald-700',
            RefundStatus::Rejected => 'bg-red-100 text-red-700',
        },
        default => 'bg-slate-100 text-slate-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {$colors}"]) }}>
    {{ $label }}
</span>
