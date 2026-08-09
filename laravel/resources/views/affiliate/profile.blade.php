@extends('affiliate.layouts.app')

@section('title', __('Profile'))
@section('breadcrumb', __('Profile'))

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl border border-slate-200 p-6">
            <h2 class="font-semibold text-slate-800 mb-4">{{ __('Edit Profile') }}</h2>
            <form method="POST" action="{{ route('affiliate.profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Phone') }}</label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <hr class="border-slate-200">

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('New Password') }}</label>
                    <input type="password" name="password"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="{{ __('Leave blank to keep current password') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Confirm New Password') }}</label>
                    <input type="password" name="password_confirmation"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                </div>

                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-lg text-sm font-medium">
                    {{ __('Save Changes') }}
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-4">
            <h2 class="font-semibold text-slate-800">{{ __('Affiliate Info') }}</h2>

            <div>
                <p class="text-xs text-slate-500">{{ __('Affiliate Code') }}</p>
                <p class="font-mono font-medium text-slate-800">{{ $affiliate->code }}</p>
            </div>

            <div>
                <p class="text-xs text-slate-500">{{ __('Commission Structure') }}</p>
                <p class="text-sm text-slate-700">
                    @if ($affiliate->commission_type === 'fixed_all_orders')
                        {{ __(':rate% on all orders', ['rate' => $affiliate->fixed_commission_rate]) }}
                    @else
                        {{ __('Per-category rates') }}
                    @endif
                </p>
            </div>

            <div>
                <p class="text-xs text-slate-500">{{ __('Joined') }}</p>
                <p class="text-sm text-slate-700">{{ $affiliate->created_at->format('Y-m-d') }}</p>
            </div>
        </div>
    </div>
@endsection
