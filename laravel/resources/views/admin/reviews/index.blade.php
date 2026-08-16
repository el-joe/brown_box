@extends('admin.layouts.app')

@section('title', __('Product Reviews'))

@section('breadcrumb')
    <span>{{ __('Product Reviews') }}</span>
@endsection

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-lg font-semibold text-slate-800">{{ __('Product Reviews') }}</h1>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-3 py-2 text-start">{{ __('Product') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Customer') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Rating') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Title') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Body') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Status') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Date') }}</th>
                    <th class="px-3 py-2 text-start">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($reviews as $review)
                    <tr class="border-b border-slate-100 hover:bg-slate-50 align-top">
                        <td class="px-3 py-2 text-slate-700">{{ $review->product?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-600">{{ $review->customer?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-600">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star text-amber-400 text-xs"></i>
                            @endfor
                        </td>
                        <td class="px-3 py-2 text-slate-600">{{ $review->title ?? '—' }}</td>
                        <td class="px-3 py-2 text-slate-600 max-w-xs truncate">{{ $review->body ?? '—' }}</td>
                        <td class="px-3 py-2">
                            @if ($review->is_approved)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">{{ __('Approved') }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">{{ __('Pending') }}</span>
                            @endif
                        </td>
                        <td class="px-3 py-2 text-slate-500 whitespace-nowrap">{{ $review->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                @unless ($review->is_approved)
                                    <form method="POST" action="{{ route('admin.reviews.approve', $review) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-emerald-600 hover:text-emerald-800" title="{{ __('Approve') }}">
                                            <i class="fa-solid fa-check"></i>
                                        </button>
                                    </form>
                                @endunless
                                <form method="POST" action="{{ route('admin.reviews.destroy', $review) }}" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="{{ __('Delete') }}">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-3 py-8 text-center text-slate-400">{{ __('No reviews found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $reviews->links() }}
    </div>
@endsection
