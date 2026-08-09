@props(['name', 'options' => [], 'selected' => null, 'multiple' => false, 'placeholder' => null])

<select
    name="{{ $name }}{{ $multiple ? '[]' : '' }}"
    @if ($multiple) multiple @endif
    data-placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => 'admin-select2 w-full']) }}
>
    @if (! $multiple && $placeholder)
        <option value=""></option>
    @endif

    @foreach ($options as $value => $label)
        <option
            value="{{ $value }}"
            @if ($multiple)
                @selected(is_array($selected) && in_array($value, $selected))
            @else
                @selected((string) $selected === (string) $value)
            @endif
        >
            {{ $label }}
        </option>
    @endforeach
</select>
