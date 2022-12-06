@php
$record = $getRecord();
@endphp

<div>
    @if (str($record['type'])->contains('image'))
        <img
            src="{{ $record['url'] }}"
             alt="{{ $record['alt'] }}"
             width="{{ $record['width'] }}"
             height="{{ $record['height'] }}"
             @if ($record['has_sizes'])
                srcset="{{ $record['large_url'] . ' 1024w, ' . $record['medium_url'] . ' 640w' }}"
                sizes="(max-width: 1200px) 100vw, 1024px"
            @endif
            loading="lazy"
            class="overflow-hidden border border-gray-300 rounded dark:border-black checkered"
        />
    @else
        <x-filament-curator::document-image
            label="{{ $record['filename'] }}"
            icon-size="xl"
            class="p-4 rounded"
        />
    @endif
</div>
