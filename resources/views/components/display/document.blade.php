@props([
    'label' => null,
    'iconSize' => 'md',
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
    'curator-document-image grid place-items-center w-full h-full text-xs uppercase relative bg-gray-100 dark:bg-gray-950/50',
    $attributes->get('class')
])>
    @if (curator()->isVideo($extension))
        @svg('heroicon-o-video-camera', ['class' => 'opacity-20 ' . $iconClasses[$iconSize]])
        <span class="block absolute">{{ $extension }}</span>
    @else
        @svg('heroicon-o-document', ['class' => 'opacity-20 ' . $iconClasses[$iconSize]])
        <span class="block absolute">{{ $extension }}</span>
    @endif
    <span class="sr-only">{{ $label }}</span>
</div>
