@extends('admin.layouts.app')

@section('title', __($label))

@section('breadcrumb')
    <a href="{{ route('admin.accounting.index') }}" class="hover:text-slate-700">{{ __('Accounting') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __($label) }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __($label) }}</h1>
        <a href="{{ route('admin.accounting.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left me-1"></i>{{ __('Back to Tree') }}
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Date') }}</th>
                    @if ($isOpeningBalance)
                        <th class="px-3 py-2 text-start">{{ __('Entity') }}</th>
                        <th class="px-3 py-2 text-end">{{ __('Amount') }}</th>
                        <th class="px-3 py-2 text-start">{{ __('Notes') }}</th>
                    @else
                        <th class="px-3 py-2 text-start">{{ __('Type') }}</th>
                        <th class="px-3 py-2 text-end">{{ __('Amount') }}</th>
                        <th class="px-3 py-2 text-start">{{ __('Description') }}</th>
                        <th class="px-3 py-2 text-start">{{ __('Reference') }}</th>
                        <th class="px-3 py-2 text-start">{{ __('Admin') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($entries as $entry)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-3 py-2 text-slate-600">{{ $entry->date?->format('Y-m-d') }}</td>
                        @if ($isOpeningBalance)
                            <td class="px-3 py-2 text-slate-600">{{ class_basename($entry->entity_type) }} #{{ $entry->entity_id }}</td>
                            <td class="px-3 py-2 text-end font-medium text-slate-800">{{ money_format((float) $entry->amount) }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $entry->notes ?? '—' }}</td>
                        @else
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 rounded-full text-xs font-medium {{ $entry->type === 'debit' ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ $entry->type === 'debit' ? __('Debit') : __('Credit') }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-end font-medium text-slate-800">{{ money_format((float) $entry->amount) }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $entry->description ?? '—' }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $entry->reference_type ? class_basename($entry->reference_type).' #'.$entry->reference_id : '—' }}</td>
                            <td class="px-3 py-2 text-slate-600">{{ $entry->admin?->name ?? '—' }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-slate-400">{{ __('No entries found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $entries->links() }}
    </div>
@endsection
