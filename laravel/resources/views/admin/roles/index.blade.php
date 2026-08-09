@extends('admin.layouts.app')

@section('title', __('Roles'))

@section('breadcrumb')
    <span>{{ __('Roles') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Roles') }}</h1>
        <a href="{{ route('admin.roles.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Role') }}
        </a>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Role') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Permissions') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Admins') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($roles as $role)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-3 py-2 text-slate-800 font-medium">
                            {{ $role->name }}
                            @if ($role->name === 'super_admin')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700 ms-1">{{ __('System') }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-slate-600">{{ $role->permissions_count }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $role->admins_count }}</td>
                        <td class="px-3 py-2 text-end whitespace-nowrap">
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-slate-400 hover:text-amber-600 me-2">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            @if ($role->name !== 'super_admin')
                                <button type="button" @click="confirmAdminDelete(@js(route('admin.roles.destroy', $role)))" class="text-slate-400 hover:text-red-600">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-3 py-8 text-center text-slate-400">{{ __('No roles found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
