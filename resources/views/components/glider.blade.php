@if (str($media->type)->contains('image'))
    <img src="{{ $source }}"
         alt="{{ $media->alt }}"
         width="{{ $media->width }}"
         height="{{ $media->height }}"
         @if ($loading)
             loading="{{ $loading }}"
         @endif
         @if ($sourceset)
            srcset="{{ $sourceset }}"
            sizes="{{ $sizes }}"
         @endif
         {{ $attributes }}
    />
@else
    <x-curator::document-image
        label="{{ $media->name }}"
        icon-size="xl"
        {{ $attributes->merge(['class' => 'p-4']) }}
    />
@endif
