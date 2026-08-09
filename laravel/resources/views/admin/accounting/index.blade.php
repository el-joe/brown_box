@extends('admin.layouts.app')

@section('title', __('Accounting'))

@section('breadcrumb')
    <span>{{ __('Accounting') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Accounting Tree') }}</h1>
    </div>

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.accounting.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Date From') }}</label>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Date To') }}</label>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </x-admin.filter-card>

    <div class="mt-6 space-y-6">
        @foreach ($tree as $section => $nodes)
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-3 bg-slate-50 border-b border-slate-200">
                    <h3 class="text-sm font-semibold text-slate-800">{{ __($section) }}</h3>
                </div>
                <table class="w-full text-sm">
                    <thead class="text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-5 py-2 text-start">{{ __('Account') }}</th>
                            <th class="px-5 py-2 text-end">{{ __('Total Debit') }}</th>
                            <th class="px-5 py-2 text-end">{{ __('Total Credit') }}</th>
                            <th class="px-5 py-2 text-end">{{ __('Net Balance') }}</th>
                            <th class="px-5 py-2 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($nodes as $node)
                            <tr class="border-t border-slate-100 hover:bg-slate-50">
                                <td class="px-5 py-2 text-slate-700">{{ __($node['label']) }}</td>
                                <td class="px-5 py-2 text-end text-red-600">{{ money_format($node['debit']) }}</td>
                                <td class="px-5 py-2 text-end text-emerald-600">{{ money_format($node['credit']) }}</td>
                                <td class="px-5 py-2 text-end font-semibold {{ $node['balance'] >= 0 ? 'text-slate-800' : 'text-red-700' }}">{{ money_format($node['balance']) }}</td>
                                <td class="px-5 py-2 text-end">
                                    <a href="{{ route('admin.accounting.show', array_merge(['node' => $node['key']], $filters)) }}" class="text-amber-600 hover:text-amber-700 text-xs font-medium">
                                        {{ __('View Entries') }} <i class="fa-solid fa-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
@endsection
