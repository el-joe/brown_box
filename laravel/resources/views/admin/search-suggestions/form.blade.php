@extends('admin.layouts.app')

@section('title', $suggestion->exists ? __('Edit Suggestion') : __('Add Suggestion'))

@section('breadcrumb')
    <a href="{{ route('admin.search-suggestions.index') }}" class="hover:text-slate-700">{{ __('Search Suggestions') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $suggestion->exists ? __('Edit') : __('Add') }}</span>
@endsection

@section('content')
    @php
        $isEdit = $suggestion->exists;
        $submitUrl = $isEdit ? route('admin.search-suggestions.update', $suggestion) : route('admin.search-suggestions.store');
    @endphp

    <form method="POST" action="{{ $submitUrl }}" class="max-w-xl">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <x-admin.card :title="__('Suggestion Details')">
            <div class="admin-field">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Keyword') }}</label>
                <input type="text" name="keyword" value="{{ old('keyword', $suggestion->keyword) }}"
                    class="w-full rounded-lg border-slate-300 text-sm">
                @error('keyword')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="admin-field mt-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Sort Order') }}</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $suggestion->sort_order ?? 0) }}"
                    class="w-full rounded-lg border-slate-300 text-sm">
            </div>

            <div class="mt-4">
                <x-admin.checkbox name="is_active" :checked="old('is_active', $suggestion->is_active ?? true)" :label="__('Active')" />
            </div>
        </x-admin.card>

        <div class="flex items-center gap-2 mt-6">
            <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                {{ $isEdit ? __('Update Suggestion') : __('Create Suggestion') }}
            </button>
            <a href="{{ route('admin.search-suggestions.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                {{ __('Cancel') }}
            </a>
        </div>
    </form>
@endsection
