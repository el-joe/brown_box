@props(['paginator'])

<div {{ $attributes->merge(['class' => '']) }}>
    {{ $paginator->links() }}
</div>
