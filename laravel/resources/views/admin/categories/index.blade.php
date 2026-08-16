@extends('admin.layouts.app')

@section('title', __('Categories'))

@section('breadcrumb')
    <span>{{ __('Categories') }}</span>
@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
@endpush

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Categories') }}</h1>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Category') }}
        </a>
    </div>

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.categories.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Parent') }}</label>
                <x-admin.select name="parent_id" :options="$parents->pluck('name', 'id')" :selected="$filters['parent_id'] ?? null" :placeholder="__('All')" />
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

    <div class="mt-6 bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto"
        x-data="{
            collapsed: new Set(),
            init() {
                new Sortable(this.$refs.tbody, {
                    handle: '.drag-handle',
                    animation: 150,
                    onEnd: () => this.persistOrder(),
                });

                this.$refs.tbody.querySelectorAll('.toggle-children').forEach((btn) => {
                    btn.addEventListener('click', () => {
                        const id = btn.dataset.id;
                        if (this.collapsed.has(id)) {
                            this.collapsed.delete(id);
                        } else {
                            this.collapsed.add(id);
                        }
                        btn.querySelector('i').classList.toggle('rotate-180');
                        this.applyVisibility();
                    });
                });
            },
            applyVisibility() {
                const rows = Array.from(this.$refs.tbody.querySelectorAll('tr[data-id]'));
                const parentOf = {};
                rows.forEach((row) => { parentOf[row.dataset.id] = row.dataset.parentId || null; });

                const isHiddenByAncestor = (id) => {
                    let pid = parentOf[id];
                    while (pid) {
                        if (this.collapsed.has(pid)) {
                            return true;
                        }
                        pid = parentOf[pid];
                    }
                    return false;
                };

                rows.forEach((row) => {
                    row.classList.toggle('hidden', isHiddenByAncestor(row.dataset.id));
                });
            },
            persistOrder() {
                const items = Array.from(this.$refs.tbody.querySelectorAll('tr[data-id]')).map((row, index) => ({
                    id: Number(row.dataset.id),
                    parent_id: row.dataset.parentId ? Number(row.dataset.parentId) : null,
                    sort_order: index,
                }));

                fetch(@js(route('admin.categories.reorder')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'X-Requested-With': 'XMLHttpRequest',
                        Accept: 'application/json',
                    },
                    body: JSON.stringify({ items }),
                });
            },
        }"
    >
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 w-8"></th>
                    <th class="px-3 py-2 w-14">{{ __('Image') }}</th>
                    <th class="px-3 py-2 w-10">{{ __('Icon') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Name') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Parent') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Status') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Sort') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody x-ref="tbody">
                @forelse ($tree as $node)
                    @include('admin.categories._row', ['nodes' => collect([$node])])
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-8 text-center text-slate-400">{{ __('No categories found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
