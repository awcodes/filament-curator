@php
    $statePath = $getStatePath();
    $items = $getState() ?? [];
    $itemsCount = count($items);
    $isMultiple = $isMultiple();
    $maxItems = $getMaxItems();

//    ray($items);
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    <div
        x-data="{
            state: $wire.entangle('{{ $statePath }}'),
            insertMedia: function (event) {
                if (event.detail.statePath !== '{{ $statePath }}') return;
                this.state = event.detail.media;
            },
        }"
        x-on:insert-media.window="insertMedia($event)"
        class="curator-media-picker w-full"
    >
        <div
            @class([
                'flex items-center gap-6 flex-wrap' => $itemsCount <= 3,
                'curator-grid-container' => $itemsCount >= 3,
            ])
            x-sortable
            wire:end.stop="mountFormComponentAction('{{ $statePath }}', 'reorder', { items: $event.target.sortable.toArray() })"
            style="{{ $itemsCount === 1 ? '--grid-column-count: 1' : '' }}"
        >
            @foreach ($items as $uuid => $item)
                <div
                    wire:key="{{ $this->getId() }}.{{ $uuid }}.{{ $field::class }}.item"
                    {{ $attributes->merge($getExtraAttributes())->class([
                        'relative block w-full overflow-hidden transition duration-75 border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white flex justify-center checkered',
                        'h-64' => ! str($item['type'])->contains('video'),
                        'md:flex-1 ' => $itemsCount <= 3,
                    ]) }}
                    x-sortable-item="{{ $uuid }}"
                >
                    @if (str($item['type'])->contains('image'))
                        <img
                            src="{{ $item['large_url'] }}"
                            alt="{{ $item['alt'] ?? $item['name'] }}"
                            @class([
                               'h-full',
                               'object-contain' => $isConstrained(),
                               'object-cover w-full' => ! $isConstrained(),
                           ])
                        />
                    @elseif (str($item['type'])->contains('video'))
                        <video controls src="{{ $item['url'] }}"></video>
                    @else
                        <x-curator::document-image
                            label="{{ $item['name'] }}"
                            icon-size="xl"
                        />
                    @endif

                    <div class="absolute top-0 right-0">
                        <div class="relative flex items-center bg-gray-900 divide-x divide-gray-700 rounded-bl-lg shadow-md">
                            @if ($isMultiple)
                                <div
                                    x-sortable-handle
                                    class="flex items-center justify-center flex-none w-10 h-10 transition text-gray-400 hover:text-gray-300"
                                >
                                    {{ $getAction('reorder') }}
                                </div>
                            @endif

                            <div class="flex items-center justify-center flex-none w-10 h-10">
                                <x-filament-actions::group
                                    :actions="[
                                        $getAction('view')(['url' => $item['url']]),
                                        $getAction('edit')(['id' => $item['id']]),
                                        $getAction('download')(['uuid' => $uuid]),
                                        $getAction('remove')(['uuid' => $uuid]),
                                    ]"
                                    color="gray"
                                    size="xs"
                                    dropdown-placement="bottom-end"
                                />
                            </div>
                        </div>

                    </div>

                    @if (! str($item['type'])->contains('video'))
                        <div class="absolute inset-x-0 bottom-0 flex items-center justify-between px-2 pt-10 pb-1 text-xs text-white bg-gradient-to-t from-black/80 to-transparent gap-3">
                            <p class="truncate">{{ $item['name'] }}.{{ $item['ext'] }}</p>
                            <p class="flex-shrink-0">{{ $item['size_for_humans'] }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <div
            @class([
                'flex items-center gap-4',
                'mt-4' => $itemsCount > 0
            ])
        >
            @if ($itemsCount === 0 || $isMultiple)
                @if (! $maxItems || $itemsCount < $maxItems)
                    {{ $getAction('open_curator_picker') }}
                @endif
            @endif
            @if ($itemsCount > 1)
                {{ $getAction('removeAll') }}
            @endif
        </div>
    </div>

</x-dynamic-component>