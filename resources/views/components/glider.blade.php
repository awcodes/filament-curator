@if ($media)
    @if (str($media->type)->contains('image'))
        <img
            src="{{ $source }}"
            alt="{{ $attributes->get('alt', $media->alt) }}"
            @if ($width && $height)
                width="{{ $width }}"
                height="{{ $height }}"
            @else
                width="{{ $media->width }}"
                height="{{ $media->height }}"
            @endif
            @if ($sourceSet)
                srcset="{{ $sourceSet }}"
                sizes="{{ $sizes }}"
            @endif
            {{ $attributes->filter(fn ($attr) => $attr !== '') }}
        />
    @else
        <x-curator::document-image
            label="{{ $media->name }}"
            icon-size="xl"
            {{ $attributes->merge(['class' => 'p-4']) }}
        />
    @endif
@endif
