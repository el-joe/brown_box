@props(['dark' => false])

<span {{ $attributes->merge(['class' => 'text-2xl font-extrabold tracking-tight ' . ($dark ? 'text-white' : 'text-ink')]) }}>ella<span class="text-brand">mart</span><span class="text-accent">+</span></span>
