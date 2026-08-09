@extends('admin.layouts.app')

@section('title', __('Search Suggestions'))

@section('breadcrumb')
    <span>{{ __('Search Suggestions') }}</span>
@endsection

@section('content')
    <div x-data="searchSuggestionsIndex()">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-lg font-semibold text-slate-800">{{ __('Search Suggestions') }}</h1>
            <div class="flex items-center gap-2">
                <button type="button" @click="$dispatch('open-modal-import-suggestions')" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    <i class="fa-solid fa-file-import me-1"></i>{{ __('Import from CSV') }}
                </button>
                <a href="{{ route('admin.search-suggestions.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    <i class="fa-solid fa-plus me-1"></i>{{ __('Add Suggestion') }}
                </a>
            </div>
        </div>

        <x-admin.filter-card class="mb-6">
            <form method="GET" action="{{ route('admin.search-suggestions.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Keyword') }}</label>
                    <input type="text" name="keyword" value="{{ $filters['keyword'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                    <x-admin.select name="is_active" :options="[1 => __('Active'), 0 => __('Inactive')]" :selected="$filters['is_active'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                        {{ __('Filter') }}
                    </button>
                </div>
            </form>
        </x-admin.filter-card>

        <form id="bulk-form" method="POST" action="{{ route('admin.search-suggestions.bulk-update') }}">
            @csrf
            <div class="flex items-center gap-2 mb-3" x-show="checked.length">
                <span class="text-xs text-slate-500" x-text="checked.length + ' {{ __('selected') }}'"></span>
                <button type="button" @click="submitBulk('enable')" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-medium hover:bg-emerald-700">
                    {{ __('Enable') }}
                </button>
                <button type="button" @click="submitBulk('disable')" class="px-3 py-1.5 rounded-lg bg-slate-600 text-white text-xs font-medium hover:bg-slate-700">
                    {{ __('Disable') }}
                </button>
            </div>

            <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-3 py-2 w-8"><input type="checkbox" @change="toggleAll($event)"></th>
                            <th class="px-3 py-2 text-start">{{ __('Keyword') }}</th>
                            <th class="px-3 py-2 text-start">{{ __('Hits') }}</th>
                            <th class="px-3 py-2 text-start">{{ __('Status') }}</th>
                            <th class="px-3 py-2 text-start">{{ __('Sort Order') }}</th>
                            <th class="px-3 py-2 text-end">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($suggestions as $suggestion)
                            <tr class="border-b border-slate-100 hover:bg-slate-50">
                                <td class="px-3 py-2">
                                    <input type="checkbox" value="{{ $suggestion->id }}" x-model="checked">
                                </td>
                                <td class="px-3 py-2 text-slate-800 font-medium">{{ $suggestion->keyword }}</td>
                                <td class="px-3 py-2 text-slate-600">{{ number_format($suggestion->hits) }}</td>
                                <td class="px-3 py-2">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $suggestion->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $suggestion->is_active ? __('Active') : __('Inactive') }}
                                    </span>
                                </td>
                                <td class="px-3 py-2 text-slate-500">{{ $suggestion->sort_order }}</td>
                                <td class="px-3 py-2 text-end whitespace-nowrap">
                                    <a href="{{ route('admin.search-suggestions.edit', $suggestion) }}" class="text-slate-400 hover:text-amber-600 me-2">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <button type="button" @click="confirmAdminDelete(@js(route('admin.search-suggestions.destroy', $suggestion)))" class="text-slate-400 hover:text-red-600">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-slate-400">{{ __('No suggestions found.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        <div class="mt-4">
            {{ $suggestions->links() }}
        </div>

        <x-admin.modal id="import-suggestions" :header="__('Import from CSV')">
            <form id="import-suggestions-form" method="POST" action="{{ route('admin.search-suggestions.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="admin-field">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('CSV File') }}</label>
                    <input type="file" name="file" accept=".csv,text/csv" class="w-full text-sm">
                    <p class="text-xs text-slate-400 mt-1">{{ __('A "keyword" column header is expected, or a single column of keywords.') }}</p>
                </div>
            </form>
            <x-slot:footer>
                <button type="submit" form="import-suggestions-form" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ __('Import') }}
                </button>
            </x-slot:footer>
        </x-admin.modal>
    </div>
@endsection

@push('scripts')
    <script>
        function searchSuggestionsIndex() {
            return {
                checked: [],

                toggleAll(event) {
                    const boxes = Array.from(document.querySelectorAll('#bulk-form tbody input[type="checkbox"]'));
                    this.checked = event.target.checked ? boxes.map((b) => Number(b.value)) : [];
                },

                submitBulk(action) {
                    const form = document.getElementById('bulk-form');
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = action;
                    form.appendChild(actionInput);

                    this.checked.forEach((id) => {
                        const idInput = document.createElement('input');
                        idInput.type = 'hidden';
                        idInput.name = 'ids[]';
                        idInput.value = id;
                        form.appendChild(idInput);
                    });

                    form.submit();
                },
            };
        }
    </script>
@endpush
