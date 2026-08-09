@props(['id' => 'admin-table', 'ajaxUrl', 'columns' => []])

{{-- $columns: array of ['data' => 'field', 'name' => 'field', 'title' => 'Label', 'orderable' => true, 'searchable' => true] --}}

<div x-data="{
        init() {
            $(this.$refs.table).DataTable({
                processing: true,
                serverSide: true,
                ajax: @js($ajaxUrl),
                columns: @js($columns),
            });
        },
    }"
>
    @isset($filters)
        <div class="mb-4 flex flex-wrap items-center gap-3">
            {{ $filters }}
        </div>
    @endisset

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table x-ref="table" id="{{ $id }}" class="table table-bordered w-full text-sm">
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
