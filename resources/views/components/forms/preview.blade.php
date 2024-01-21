@php
    $record = $getRecord();
@endphp

<div>
    @if ($record->is_previewable)
        <img
            src="{{ $record->url }}"
            alt="{{ $record->alt }}"
            width="{{ $record->width }}"
            height="{{ $record->height }}"
            loading="lazy"
            class="overflow-hidden border border-gray-300 rounded dark:border-gray-900 checkered"
        />
    @elseif ($record->is_video)
        <video controls src="{{ $record->url }}"></video>
    @else
        <x-curator::display.document
            label="{{ $record->name }}"
            icon-size="xl"
            class="p-4 rounded"
            :type="$record->type"
        />
    @endif
</div>
