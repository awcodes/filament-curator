@if ($mediaItem)
    @if ($mediaItem->isPreviewable())
        <img
            src="{{ $source }}"
            alt="{{ $mediaItem->getAlt() }}"
            width="{{ $width ?? $mediaItem->getWidth() }}"
            height="{{ $height ?? $mediaItem->getHeight() }}"
            @if ($sourceSet)
                srcset="{{ $sourceSet }}"
                sizes="{{ $sizes }}"
            @endif
            {{ $attributes->filter(fn ($attr) => $attr !== '') }}
        />
    @else
        <x-curator::document-image
            label="{{ $mediaItem->getName() }}"
            icon-size="xl"
            {{ $attributes->merge(['class' => 'p-4']) }}
        />
    @endif
@endif
