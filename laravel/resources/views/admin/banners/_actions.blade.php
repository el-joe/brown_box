<a href="{{ route('admin.banners.edit', $banner) }}" class="text-slate-400 hover:text-amber-600 me-2">
    <i class="fa-solid fa-pen"></i>
</a>
<button type="button" @click="confirmAdminDelete(@js(route('admin.banners.destroy', $banner)))" class="text-slate-400 hover:text-red-600">
    <i class="fa-solid fa-trash"></i>
</button>
