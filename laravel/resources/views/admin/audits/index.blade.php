@extends('admin.layouts.app')

@section('title', __('Audit Log'))

@section('breadcrumb')
    <span>{{ __('Audit Log') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Audit Log') }}</h1>
    </div>

    <x-admin.filter-card class="mb-6">
        <form method="GET" action="{{ route('admin.audits.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            @if ($admins->isNotEmpty())
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Admin') }}</label>
                    <x-admin.select name="admin_id" :options="$admins->pluck('name', 'id')" :selected="$filters['admin_id'] ?? null" :placeholder="__('All')" />
                </div>
            @endif
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Model') }}</label>
                <x-admin.select name="model_type" :options="$modelTypes->mapWithKeys(fn ($type) => [$type => class_basename($type)])" :selected="$filters['model_type'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Action') }}</label>
                <x-admin.select name="action" :options="['created' => __('Created'), 'updated' => __('Updated'), 'deleted' => __('Deleted')]" :selected="$filters['action'] ?? null" :placeholder="__('All')" />
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('From') }}</label>
                <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('To') }}</label>
                <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </x-admin.filter-card>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto" x-data="{ expanded: null }">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Admin') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Action') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Model') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Model ID') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Description') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Changes') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('IP') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Date') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($activities as $activity)
                    @php
                        $badge = match ($activity->event) {
                            'created' => 'bg-emerald-100 text-emerald-700',
                            'updated' => 'bg-blue-100 text-blue-700',
                            'deleted' => 'bg-red-100 text-red-700',
                            default => 'bg-slate-100 text-slate-700',
                        };
                    @endphp
                    <tr class="border-b border-slate-100 hover:bg-slate-50 align-top">
                        <td class="px-3 py-2 text-slate-700">{{ $activity->causer?->name ?? '—' }}</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $badge }}">
                                {{ ucfirst($activity->event ?? '—') }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-slate-600">{{ class_basename($activity->subject_type) }}</td>
                        <td class="px-3 py-2 text-slate-600">
                            @if ($activity->subject_id)
                                #{{ $activity->subject_id }}
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-3 py-2 text-slate-600">{{ $activity->description }}</td>
                        <td class="px-3 py-2">
                            @php $properties = $activity->properties; @endphp
                            @if ($properties->has('old') || $properties->has('attributes'))
                                <button type="button" class="text-xs text-amber-600 hover:underline"
                                    @click="expanded = expanded === {{ $activity->id }} ? null : {{ $activity->id }}">
                                    {{ __('View') }}
                                </button>
                                <div x-show="expanded === {{ $activity->id }}" x-cloak class="mt-2 space-y-2 max-w-xs">
                                    @if ($properties->has('old'))
                                        <div>
                                            <p class="text-xs font-semibold text-slate-500">{{ __('Old') }}</p>
                                            <pre class="text-xs bg-slate-50 rounded p-2 overflow-x-auto">{{ json_encode($properties->get('old'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    @endif
                                    @if ($properties->has('attributes'))
                                        <div>
                                            <p class="text-xs font-semibold text-slate-500">{{ __('New') }}</p>
                                            <pre class="text-xs bg-slate-50 rounded p-2 overflow-x-auto">{{ json_encode($properties->get('attributes'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        </div>
                                    @endif
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-3 py-2 text-slate-500">{{ $activity->properties->get('ip', '—') }}</td>
                        <td class="px-3 py-2 text-slate-500 whitespace-nowrap">{{ $activity->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-8 text-center text-slate-400">{{ __('No audit records found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $activities->links() }}
    </div>
@endsection
