<button type="button"
    x-data="{ featured: {{ $product->is_featured ? 'true' : 'false' }} }"
    @click="fetch(@js(route('admin.products.toggle-featured', $product)), { method: 'PATCH', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' } }).then(r => r.json()).then(d => featured = d.is_featured)"
    class="text-lg"
    :class="featured ? 'text-amber-500' : 'text-slate-300'">
    <i :class="featured ? 'fa-solid fa-star' : 'fa-regular fa-star'"></i>
</button>
