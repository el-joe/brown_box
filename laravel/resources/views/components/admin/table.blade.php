@props(['id' => 'admin-table', 'ajaxUrl', 'columns' => []])

{{-- $columns: array of ['data' => 'field', 'name' => 'field', 'title' => 'Label', 'orderable' => true, 'searchable' => true] --}}

<div x-data="{
        init() {
            $(this.$refs.table).DataTable({
                processing: true,
                serverSide: true,
                ajax: @js($ajaxUrl),
                columns: @js($columns),
                language: {
                    processing: '',
                    search: '',
                    searchPlaceholder: @js(__('Search…')),
                    lengthMenu: '_MENU_',
                    info: @js(__('Showing _START_ to _END_ of _TOTAL_ entries')),
                    infoEmpty: @js(__('No entries found')),
                    infoFiltered: @js(__('(filtered from _MAX_ total entries)')),
                    zeroRecords: @js(__('No matching records found')),
                    paginate: { previous: '<i class=\'fa-solid fa-chevron-left\'></i>', next: '<i class=\'fa-solid fa-chevron-right\'></i>' },
                },
                dom: '<\'admin-table-toolbar\'<\'admin-table-length\'l><\'admin-table-search\'f>>rt<\'admin-table-footer\'<\'admin-table-info\'i><\'admin-table-pagination\'p>>',
            });
        },
    }"
>
    @isset($filters)
        <div class="mb-4 flex flex-wrap items-center gap-3">
            {{ $filters }}
        </div>
    @endisset

    <div class="admin-table-card bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table x-ref="table" id="{{ $id }}" class="admin-table table w-full text-sm">
                <thead>
                    <tr>
                        @foreach ($columns as $column)
                            <th>{{ $column['title'] ?? $column['data'] }}</th>
                        @endforeach
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
