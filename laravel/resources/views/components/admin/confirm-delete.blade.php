<div
    x-data="{
        open: false,
        action: null,
        init() {
            window.confirmAdminDelete = (action) => {
                this.action = action;
                this.open = true;
            };
        },
    }"
    x-show="open"
    x-cloak
    @keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
>
    <div x-show="open" x-transition.opacity @click="open = false" class="absolute inset-0 bg-slate-900/50"></div>

    <div x-show="open" x-transition @click.stop class="relative bg-white rounded-xl shadow-xl w-full max-w-sm p-6 text-center">
        <div class="mx-auto w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mb-4">
            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
        </div>
        <h3 class="text-sm font-semibold text-slate-800 mb-1">{{ __('Are you sure?') }}</h3>
        <p class="text-sm text-slate-500 mb-5">{{ __('This action cannot be undone.') }}</p>

        <div class="flex items-center justify-center gap-2">
            <button type="button" @click="open = false" class="px-4 py-2 text-sm rounded-lg border border-slate-300 text-slate-700 hover:bg-slate-50">
                {{ __('Cancel') }}
            </button>
            <form :action="action" method="POST" @submit="open = false">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 text-sm rounded-lg bg-red-600 text-white hover:bg-red-700">
                    {{ __('Delete') }}
                </button>
            </form>
        </div>
    </div>
</div>
