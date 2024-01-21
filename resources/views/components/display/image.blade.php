@props([
    'item' => null,
    'lazy' => true,
    'constrained' => true,
])
])

<img
    src="{{ $src }}"
    alt="{{ $alt ?? '' }}"
    {{ $attributes->merge() }}
    @if ($lazy)
        loading="lazy"
    @endif
    @class([
       'h-full',
       'object-contain' => $constrained,
       'object-cover w-full' => ! $constrained,
    ])
    {{ $attributes->merge() }}
/>