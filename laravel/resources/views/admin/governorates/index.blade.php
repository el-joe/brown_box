@extends('admin.layouts.app')

@section('title', __('Governorates'))

@section('breadcrumb')
    <span>{{ __('Governorates') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Governorates') }}</h1>
        <a href="{{ route('admin.governorates.create') }}" class="px-4 py-2 rounded-lg bg-amber-600 text-white text-sm font-medium hover:bg-amber-700">
            <i class="fa-solid fa-plus me-1"></i>{{ __('Add Governorate') }}
        </a>
    </div>

    <x-admin.filter-card>
        <form method="GET" action="{{ route('admin.governorates.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div class="admin-field">
                <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Name') }}</label>
                <input type="text" name="name" value="{{ $filters['name'] ?? '' }}" class="w-full rounded-lg border-slate-300 text-sm">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-medium hover:bg-slate-900">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </x-admin.filter-card>

    <div class="mt-6 bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Name (EN)') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Name (AR)') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Country') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Cities') }}</th>
                    <th class="px-3 py-2 text-end">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($governorates as $governorate)
                    <tr class="border-b border-slate-100 hover:bg-slate-50">
                        <td class="px-3 py-2 text-sm font-medium text-slate-800">{{ $governorate->name_en }}</td>
                        <td class="px-3 py-2 text-sm text-slate-600" dir="rtl">{{ $governorate->name_ar }}</td>
                        <td class="px-3 py-2 text-sm text-slate-600">{{ $governorate->country?->name_en ?? '—' }}</td>
                        <td class="px-3 py-2 text-sm">
                            <a href="{{ route('admin.governorates.cities', $governorate) }}" class="text-amber-600 hover:underline">
                                {{ $governorate->cities_count }} {{ __('cities') }}
                            </a>
                        </td>
                        <td class="px-3 py-2 text-end whitespace-nowrap">
                            <a href="{{ route('admin.governorates.cities', $governorate) }}" class="text-slate-400 hover:text-amber-600 me-2" title="{{ __('Cities') }}">
                                <i class="fa-solid fa-city"></i>
                            </a>
                            <a href="{{ route('admin.governorates.edit', $governorate) }}" class="text-slate-400 hover:text-amber-600 me-2">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button type="button" @click="confirmAdminDelete(@js(route('admin.governorates.destroy', $governorate)))" class="text-slate-400 hover:text-red-600">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-8 text-center text-slate-400">{{ __('No governorates found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
