@props([
    'label' => null,
    'iconSize' => 'md',
    'type' => 'file'
])

@php
$iconClasses = [
    'sm' => 'w-4 h-4',
    'md' => 'w-8 h-8',
    'lg' => 'w-10 h-10',
    'xl' => 'w-12 h-12',
];
@endphp

<div @class([
    'curator-document-image grid place-items-center w-full h-full text-sm',
    $attributes->get('class')
])>
    @if (str($type)->contains('video'))
        @svg('heroicon-s-video-camera', ['class' => $iconClasses[$iconSize]])
    @else
        @svg('heroicon-s-document', ['class' => $iconClasses[$iconSize]])
    @endif
    <span class="sr-only">{{ $label }}</span>
</div>
