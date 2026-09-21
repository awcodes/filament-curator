@if ($mediaItem)
    @if ($mediaItem->isPreviewable())
        <img
            src="{{ $source }}"
            alt="{{ $mediaItem->getAlt() }}"
            @if (filled($displayWidth))
                width="{{ $displayWidth }}"
            @endif
            @if (filled($displayHeight))
                height="{{ $displayHeight }}"
            @endif
            @if ($sourceSet)
                srcset="{{ $sourceSet }}"
                sizes="{{ $sizes }}"
            @endif
            {{ $attributes->filter(fn ($attr) => $attr !== '') }}
        />
    @else
        <x-curator::display.document
            label="{{ $mediaItem->getName() }}"
            :extension="$mediaItem->getExtension()"
            icon-size="xl"
            {{ $attributes->merge(['class' => 'p-4']) }}
        />
    @endif
@endif
