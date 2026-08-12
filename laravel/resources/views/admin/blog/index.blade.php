@extends('admin.layouts.app')

@section('title', __('Blog'))

@section('breadcrumb')
    <span>{{ __('Blog') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Blog') }}</h1>
        <a href="{{ route('admin.blog.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Post') }}
        </a>
    </div>

    <x-admin.table
        id="blog-posts-table"
        :ajax-url="route('admin.blog.data')"
        :columns="[
            ['data' => 'thumbnail', 'name' => 'thumbnail', 'title' => __('Thumbnail'), 'orderable' => false, 'searchable' => false],
            ['data' => 'title_en', 'name' => 'title_en', 'title' => __('Title (EN)')],
            ['data' => 'title_ar', 'name' => 'title_ar', 'title' => __('Title (AR)')],
            ['data' => 'category_name', 'name' => 'category_name', 'title' => __('Category'), 'orderable' => false, 'searchable' => false],
            ['data' => 'published_at', 'name' => 'published_at', 'title' => __('Published At')],
            ['data' => 'status', 'name' => 'status', 'title' => __('Status'), 'orderable' => false, 'searchable' => false],
            ['data' => 'actions', 'name' => 'actions', 'title' => __('Actions'), 'orderable' => false, 'searchable' => false],
        ]"
    >
        <x-slot:filters>
            <form method="GET" action="{{ route('admin.blog.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 w-full">
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Title') }}</label>
                    <input type="text" name="title" value="{{ $filters['title'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Category') }}</label>
                    <x-admin.select name="blog_category_id" :options="$categories->pluck('name', 'id')" :selected="$filters['blog_category_id'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="admin-field">
                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Status') }}</label>
                    <x-admin.select name="is_published" :options="[1 => __('Published'), 0 => __('Draft')]" :selected="$filters['is_published'] ?? null" :placeholder="__('All')" />
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                        {{ __('Filter') }}
                    </button>
                </div>
            </form>
        </x-slot:filters>
    </x-admin.table>
@endsection
