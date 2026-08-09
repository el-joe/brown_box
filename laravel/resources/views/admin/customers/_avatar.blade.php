@if ($customer->avatar)
    <img src="{{ asset_url($customer->avatar) }}" class="w-8 h-8 rounded-full object-cover border border-slate-200">
@else
    <div class="w-8 h-8 rounded-full bg-slate-200 text-slate-500 flex items-center justify-center text-xs font-semibold">
        {{ mb_strtoupper(mb_substr($customer->name, 0, 1)) }}
    </div>
@endif
