<div>
    @if (str($media->type)->contains('image'))
        <img src="{{ $media->url }}"
             alt="{{ $media->alt }}"
             width="{{ $media->width }}"
             height="{{ $media->height }}"
             loading="lazy"
             @if ($media->has_sizes)
                srcset="{{ $media->large_url . ' 1024w, ' . $media->medium_url . ' 640w' }}"
                sizes="(max-width: 1200px) 100vw, 1024px"
             @endif
             class="overflow-hidden border border-gray-300 rounded dark:border-black checkered"
        />
    @else
        <x-filament-curator::document-image
                label="{{ $media->filename }}"
                icon-size="xl"
                class="p-4 rounded"
        />
    @endif
</div>