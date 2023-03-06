@props([
    'label' => null,
    'iconSize' => 'md',
    'type' => 'file',
    'extension' => 'pdf',
])

@php
$iconClasses = [
    'sm' => 'w-4 h-4',
    'md' => 'w-6 h-6',
    'lg' => 'w-16 h-16',
    'xl' => 'w-24 h-24',
];
@endphp

<div @class([
    'curator-document-image grid place-items-center w-full h-full text-xs uppercase relative',
    $attributes->get('class')
])>
    @if (str($type)->contains('video'))
        @svg('heroicon-o-video-camera', ['class' => 'opacity-20 ' . $iconClasses[$iconSize]])
        <span class="block absolute">{{ $extension }}</span>
    @else
        @svg('heroicon-o-document', ['class' => 'opacity-20 ' . $iconClasses[$iconSize]])
        <span class="block absolute">{{ $extension }}</span>
    @endif
    <span class="sr-only">{{ $label }}</span>
</div>
