<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $customer->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
    {{ $customer->is_active ? __('Active') : __('Inactive') }}
</span>
