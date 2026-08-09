@php
    $from = $from ?? null;
    $to = $to ?? null;
    $exportType = $exportType ?? null;
    $groupBy = $groupBy ?? false;
@endphp

<div class="flex items-center justify-between mb-6 flex-wrap gap-3">
    <form method="GET" action="{{ $action }}" class="flex items-end gap-3 flex-wrap">
        <div class="admin-field">
            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('From') }}</label>
            <input type="date" name="date_from" value="{{ old('date_from', $from?->format('Y-m-d')) }}" class="rounded-lg border-slate-300 text-sm">
        </div>
        <div class="admin-field">
            <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('To') }}</label>
            <input type="date" name="date_to" value="{{ old('date_to', $to?->format('Y-m-d')) }}" class="rounded-lg border-slate-300 text-sm">
        </div>
        @if ($groupBy)
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Group By') }}</label>
                <x-admin.select name="group_by" :options="['daily' => __('Daily'), 'weekly' => __('Weekly'), 'monthly' => __('Monthly')]" :selected="request('group_by', 'daily')" />
            </div>
        @endif
        <button type="submit" class="px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">{{ __('Apply') }}</button>
    </form>

    @if ($exportType)
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reports.export', array_merge(['type' => $exportType, 'format' => 'excel'], request()->query())) }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-file-excel me-1"></i>{{ __('Excel') }}
            </a>
            <a href="{{ route('admin.reports.export', array_merge(['type' => $exportType, 'format' => 'pdf'], request()->query())) }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-file-pdf me-1"></i>{{ __('PDF') }}
            </a>
        </div>
    @endif
</div>
