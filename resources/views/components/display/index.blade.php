@props([
    'item' => null,
    'src' => null,
    'controls' => null,
    'lazy' => null,
    'player' => false,
    'iconClasses' => '',
    'constrained' => false,
])

@php
    if ($item && is_array($item)) {
        $item = (object) $item;
    }

    if (!$src) {
      $src = $item->url;
    }
@endphp

@if (curator()->isPreviewable($item->ext))
    <img
        src="{{ $src }}"
        alt="{{ $item->alt ?? '' }}"
        loading="{{ $lazy ? 'lazy' : 'eager' }}"
        {{
            $attributes
                ->merge(['width' => $item->width, 'height' => $item->height])
                ->except(['src', 'alt', 'lazy', 'item'])
                ->class([
                    'object-cover' => ! $constrained,
                    'object-contain' => $constrained,
                ])
        }}
    />
@elseif (curator()->isVideo($item->ext) && $player)
    <video
        src="{{ $src }}"
        @if ($controls)
            controls
        @endif
        preload="{{ $lazy ? 'none' : 'auto' }}"
        {{ $attributes->except(['src', 'controls', 'lazy', 'item']) }}
    ></video>
@else
    <div
        @class([
            'curator-document-image grid place-items-center w-full h-full text-xs uppercase relative',
            $attributes->get('class')
        ])
        {{ $attributes->except(['src', 'alt', 'lazy', 'item', 'class']) }}
    >
        @if (curator()->isVideo($item->ext))
            @svg('heroicon-o-film', ['class' => 'opacity-20 ' . $iconClasses])
            <span class="block absolute">{{ $item->ext }}</span>
        @else
            @svg('heroicon-o-document', ['class' => 'opacity-20 ' . $iconClasses])
            <span class="block absolute">{{ $item->ext }}</span>
        @endif
        <span class="sr-only">{{ $item->pretty_name }}</span>
    </div>
@endif