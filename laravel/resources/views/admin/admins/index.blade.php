@extends('admin.layouts.app')

@section('title', __('Admins'))

@section('breadcrumb')
    <span>{{ __('Admins') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Admins') }}</h1>
        <a href="{{ route('admin.admins.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Admin') }}
        </a>
    </div>

    <x-admin.filter-card class="mb-6">
        <form method="GET" action="{{ route('admin.admins.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
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

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 w-14">{{ __('Avatar') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Name') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Email') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Roles') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Last Login') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Status') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($admins as $admin)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-3 py-2">
                            @if ($admin->avatar)
                                <img src="{{ asset_url($admin->avatar) }}" class="w-9 h-9 rounded-full object-cover border border-slate-200">
                            @else
                                <div class="w-9 h-9 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-xs font-semibold">
                                    {{ mb_strtoupper(mb_substr($admin->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-slate-800 font-medium">{{ $admin->name }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $admin->email }}</td>
                        <td class="px-3 py-2">
                            @forelse ($admin->roles as $role)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 me-1">
                                    {{ $role->name }}
                                </span>
                            @empty
                                <span class="text-slate-400">—</span>
                            @endforelse
                        </td>
                        <td class="px-3 py-2 text-slate-600">{{ $admin->last_login_at?->format('Y-m-d H:i') ?? '—' }}</td>
                        <td class="px-3 py-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $admin->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $admin->is_active ? __('Active') : __('Inactive') }}
                            </span>
                        </td>
                        <td class="px-3 py-2 text-end whitespace-nowrap">
                            <a href="{{ route('admin.admins.edit', $admin) }}" class="text-slate-400 hover:text-amber-600 me-2">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            @if ($admin->id !== auth('admin')->id())
                                <button type="button" @click="confirmAdminDelete(@js(route('admin.admins.destroy', $admin)))" class="text-slate-400 hover:text-red-600">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-8 text-center text-slate-400">{{ __('No admins found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $admins->links() }}
    </div>
@endsection
