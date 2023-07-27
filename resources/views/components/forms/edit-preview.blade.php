@php
 dd($getStatePath())
@endphp
<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="flex justify-center mb-4 overflow-hidden border border-gray-300 rounded dark:border-gray-700 checkered h-48 flex-shrink-0 relative">
{{--        @if (str($record->type)->contains('image'))--}}
{{--            <img--}}
{{--                src="{{ $record->url }}"--}}
{{--                alt="{{ $record->alt }}"--}}
{{--                width="{{ $record->width }}"--}}
{{--                height="{{ $record->height }}"--}}
{{--                loading="lazy"--}}
{{--                class="overflow-hidden border border-gray-300 rounded dark:border-gray-900 checkered"--}}
{{--            />--}}
{{--        @elseif (str($record->type)->contains('video'))--}}
{{--            <video controls src="{{ $record->url }}"></video>--}}
{{--        @else--}}
{{--            <x-curator::document-image--}}
{{--                label="{{ $record->name }}"--}}
{{--                icon-size="xl"--}}
{{--                class="p-4 rounded"--}}
{{--                :type="$record->type"--}}
{{--            />--}}
{{--        @endif--}}

        <div class="absolute top-0 right-0 flex bg-gray-900 divide-x divide-gray-700 rounded-bl-lg shadow-md">
            {{ $this->viewAction }}
            {{ $this->downloadAction }}
            {{ $this->destroyAction }}
        </div>
    </div>
</x-dynamic-component>