<button type="button"
    x-data="{ hero: {{ $product->is_hero ? 'true' : 'false' }} }"
    @click="fetch(@js(route('admin.products.toggle-hero', $product)), { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } }).then(r => r.json()).then(d => hero = d.is_hero)"
    class="text-lg"
    :class="hero ? 'text-purple-500' : 'text-slate-300'">
    <i :class="hero ? 'fa-solid fa-bolt' : 'fa-regular fa-bolt'"></i>
</button>
