@php
    $record = $getRecord();
@endphp

<div>
    @if (str($record->type)->contains('image'))
        <img
            src="{{ $record->url }}"
            alt="{{ $record->alt }}"
            width="{{ $record->width }}"
            height="{{ $record->height }}"
            loading="lazy"
            class="overflow-hidden border border-gray-300 rounded dark:border-gray-900 checkered"
        />
    @elseif (str($record->type)->contains('video'))
        <video controls src="{{ $record->url }}"></video>
    @else
        <x-curator::document-image
            label="{{ $record->name }}"
            icon-size="xl"
            class="p-4 rounded"
            :type="$record->type"
            :extension="$record->ext"
        />
    @endif
</div>
