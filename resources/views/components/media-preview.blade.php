@php
$record = $getRecord();
@endphp

<div>
    @if (str($record['type'])->contains('image'))
        @if ($record['has_sizes'])
            <img src="{{ $record['large_url'] }}"
                alt="{{ $record['alt'] }}"
                width="{{ $record['width'] }}"
                height="{{ $record['height'] }}"
                srcset="{{ $record['large_url'] . ' 1024w, ' . $record['medium_url'] . ' 640w' }}"
                sizes="(max-width: 1200px) 100vw, 1024px"
                loading="lazy"
                class="overflow-hidden border border-gray-300 rounded dark:border-black checkered"
            />
        @else
            <img src="{{ $record['url'] }}"
                alt="{{ $record['alt'] }}"
                width="{{ $record['width'] }}"
                height="{{ $record['height'] }}"
                loading="lazy"
                class="overflow-hidden border border-gray-300 rounded dark:border-black checkered"
            />
        @endif
    @else
        <x-filament-curator::document-image
            label="{{ $record['filename'] }}"
            icon-size="xl"
            class="p-4 rounded"
        />
    @endif
</div>
