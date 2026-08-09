@extends('admin.layouts.app')

@section('title', $admin->exists ? __('Edit Admin') : __('Add Admin'))

@section('breadcrumb')
    <a href="{{ route('admin.admins.index') }}" class="hover:text-amber-600">{{ __('Admins') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $admin->exists ? __('Edit') : __('Add') }}</span>
@endsection

@section('content')
    @php
        $isEdit = $admin->exists;
        $submitUrl = $isEdit ? route('admin.admins.update', $admin) : route('admin.admins.store');
    @endphp

    <h1 class="text-lg font-semibold text-slate-800 mb-6">{{ $isEdit ? __('Edit Admin') : __('Add Admin') }}</h1>

    <form method="POST" action="{{ $submitUrl }}" enctype="multipart/form-data">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <x-admin.card :title="__('General')">
                    <div class="admin-field">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name') }}</label>
                        <input type="text" name="name" value="{{ old('name', $admin->name) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="admin-field mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Email') }}</label>
                        <input type="email" name="email" value="{{ old('email', $admin->email) }}" class="w-full rounded-lg border-slate-300 text-sm">
                        @error('email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="admin-field mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">
                            {{ __('Password') }}
                            @if ($isEdit) <span class="text-xs text-slate-400 font-normal">({{ __('leave blank to keep current') }})</span> @endif
                        </label>
                        <input type="password" name="password" class="w-full rounded-lg border-slate-300 text-sm" autocomplete="new-password">
                        @error('password') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="admin-field mt-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Roles') }}</label>
                        <x-admin.select name="roles" multiple :options="$roles->pluck('name', 'id')" :selected="old('roles', $admin->roles->pluck('id')->all())" />
                        @error('roles') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </x-admin.card>
            </div>

            <div class="space-y-6">
                <x-admin.card :title="__('Avatar')">
                    <x-admin.image-upload name="avatar" :current="$admin->avatar" />
                </x-admin.card>

                <x-admin.card :title="__('Status')">
                    <x-admin.checkbox name="is_active" :checked="old('is_active', $admin->is_active ?? true)" :label="__('Active')" />
                </x-admin.card>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                        {{ $isEdit ? __('Update Admin') : __('Create Admin') }}
                    </button>
                    <a href="{{ route('admin.admins.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>
        </div>
    </form>
@endsection
