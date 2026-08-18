@props(['dark' => false])

@php
    $logoPath = setting('site_logo');
    $siteName = setting('site_name_' . current_lang()) ?: setting('site_name_en', config('app.name'));
@endphp

@if ($logoPath)
    <img
        src="{{ asset_url($logoPath) }}"
        alt="{{ $siteName }}"
        {{ $attributes->merge(['class' => 'h-9 w-auto object-contain' . ($dark ? ' brightness-0 invert' : '')]) }}
    >
@else
    <span {{ $attributes->merge(['class' => 'text-2xl font-extrabold tracking-tight ' . ($dark ? 'text-white' : 'text-ink')]) }}>
        {{ $siteName }}
    </span>
@endif
