@extends('admin.layouts.app')

@section('title', __('Send Newsletter'))

@section('breadcrumb')
    <a href="{{ route('admin.subscribers.index') }}" class="hover:text-slate-700">{{ __('Subscribers') }}</a>
    <span class="mx-1">/</span>
    <span>{{ __('Send Newsletter') }}</span>
@endsection

@section('content')
    <div x-data="newsletterComposeForm()">
        <form id="newsletter-compose-form" method="POST" action="{{ route('admin.subscribers.send') }}"
            @submit="beforeSubmit()">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <x-admin.card :title="__('Newsletter Content')">
                        <div class="admin-field">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Subject') }}</label>
                            <input type="text" name="subject" required value="{{ old('subject') }}"
                                class="w-full rounded-lg border-slate-300 text-sm">
                            @error('subject')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="admin-field mt-4">
                            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Body') }}</label>
                            <div id="newsletter-body-editor" style="min-height: 320px;">{!! old('body') !!}</div>
                            <textarea name="body" data-editor-source="newsletter-body-editor" class="hidden">{{ old('body') }}</textarea>
                            @error('body')
                                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </x-admin.card>
                </div>

                <div class="space-y-6">
                    <x-admin.card :title="__('Recipients')">
                        <p class="text-sm text-slate-600">
                            {{ __('This will be sent to :count active subscriber(s).', ['count' => $activeCount]) }}
                        </p>
                    </x-admin.card>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                            {{ __('Send Newsletter') }}
                        </button>
                        <a href="{{ route('admin.subscribers.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                            {{ __('Cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.min.js"></script>
    <script>
        function newsletterComposeForm() {
            return {
                init() {
                    const el = document.getElementById('newsletter-body-editor');
                    if (!el || !window.Quill) return;
                    el.__quill = new Quill(el, { theme: 'snow' });
                },

                beforeSubmit() {
                    const el = document.getElementById('newsletter-body-editor');
                    const target = document.querySelector('[data-editor-source="newsletter-body-editor"]');
                    if (el && el.__quill && target) {
                        target.value = el.__quill.root.innerHTML;
                    }
                },
            };
        }
    </script>
@endpush
