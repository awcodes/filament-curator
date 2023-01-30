@if ($media)
    @if (str($media->type)->contains('image'))
        <img
            src="{{ $source }}"
            alt="{{ $media->alt }}"
            width="{{ $media->width }}"
            height="{{ $media->height }}"
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