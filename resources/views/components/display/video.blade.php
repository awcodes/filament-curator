@props([
    'controls' => true,
    'src' => null,
    'lazy' => true,
])

<video
    src="{{ $item->url }}"
    @if ($controls)
        controls
    @endif
    @if ($lazy)
        preload="none"
    @endif
    {{ $attributes->merge() }}
></video>