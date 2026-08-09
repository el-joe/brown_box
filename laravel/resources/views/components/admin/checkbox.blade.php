@props(['name', 'value' => 1, 'checked' => false, 'label' => null])

<label {{ $attributes->only('class')->merge(['class' => 'admin-field inline-flex items-center gap-2 text-sm text-slate-700']) }}>
    <input
        type="checkbox"
        name="{{ $name }}"
        value="{{ $value }}"
        @checked($checked)
        {{ $attributes->except(['class', 'name', 'value', 'checked']) }}
        class="rounded border-slate-300 text-amber-600 focus:ring-amber-500"
    >
    @if ($label)
        <span>{{ $label }}</span>
    @endif
</label>
