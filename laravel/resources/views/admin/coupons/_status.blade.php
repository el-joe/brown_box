<button type="button"
    x-data="{ active: {{ $coupon->is_active ? 'true' : 'false' }} }"
    @click="fetch(@js(route('admin.coupons.toggle-active', $coupon)), { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } }).then(r => r.json()).then(d => active = d.is_active)"
    class="px-2 py-1 rounded-full text-xs font-medium"
    :class="active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500'">
    <span x-text="active ? @js(__('Active')) : @js(__('Inactive'))"></span>
</button>
