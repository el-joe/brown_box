@extends('admin.layouts.app')

@section('title', $role->exists ? __('Edit Role') : __('Add Role'))

@section('breadcrumb')
    <a href="{{ route('admin.roles.index') }}" class="hover:text-amber-600">{{ __('Roles') }}</a>
    <span class="mx-1">/</span>
    <span>{{ $role->exists ? __('Edit') : __('Add') }}</span>
@endsection

@section('content')
    @php
        $isEdit = $role->exists;
        $submitUrl = $isEdit ? route('admin.roles.update', $role) : route('admin.roles.store');
        $isLocked = $isEdit && $role->name === 'super_admin';
    @endphp

    <h1 class="text-lg font-semibold text-slate-800 mb-6">{{ $isEdit ? __('Edit Role') : __('Add Role') }}</h1>

    <form method="POST" action="{{ $submitUrl }}">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="space-y-6">
            <x-admin.card :title="__('Role Name')">
                <div class="admin-field max-w-sm">
                    <input type="text" name="name" value="{{ old('name', $role->name) }}" @disabled($isLocked)
                        class="w-full rounded-lg border-slate-300 text-sm">
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    @if ($isLocked)
                        <p class="text-xs text-slate-400 mt-1">{{ __('The super admin role cannot be renamed and always has all permissions.') }}</p>
                    @endif
                </div>
            </x-admin.card>

            <x-admin.card :title="__('Permissions')">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4" x-data>
                    @foreach ($groups as $group => $permissions)
                        @continue($permissions->isEmpty())
                        <div class="border border-slate-200 rounded-lg p-4" x-data="{
                            ids: {{ Js::from($permissions->pluck('id')->all()) }},
                            get all() { return this.ids.every(id => $refs['group-{{ Str::slug($group) }}'].querySelector(`input[value='${id}']`).checked); },
                        }">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-sm font-semibold text-slate-700">{{ __($group) }}</span>
                                <label class="text-xs text-slate-500 flex items-center gap-1 cursor-pointer">
                                    <input type="checkbox" :checked="all"
                                        @change="$refs['group-{{ Str::slug($group) }}'].querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = $event.target.checked)"
                                        class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                    {{ __('Select all') }}
                                </label>
                            </div>
                            <div x-ref="group-{{ Str::slug($group) }}" class="space-y-2">
                                @foreach ($permissions as $permission)
                                    <label class="flex items-center gap-2 text-sm text-slate-600">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                            @checked(in_array($permission->id, old('permissions', $assigned)))
                                            @disabled($isLocked)
                                            class="rounded border-slate-300 text-amber-600 focus:ring-amber-500">
                                        {{ $permission->name }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-admin.card>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
                    {{ $isEdit ? __('Update Role') : __('Create Role') }}
                </button>
                <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-sm text-slate-700 hover:bg-slate-50">
                    {{ __('Cancel') }}
                </a>
            </div>
        </div>
    </form>
@endsection
