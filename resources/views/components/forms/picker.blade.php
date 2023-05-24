@php
    $statePath = $getStatePath();
    $items = $getState();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-action="$getHintAction()"
    :hint-color="$getHintColor()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$statePath"
>

    <div
        x-data="{
            state: $wire.entangle('{{ $statePath }}'),
            removeItem: function (index) {
                delete this.state[index];
            },
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
                'flex items-center gap-6 flex-wrap' => count($items) <= 3,
                'curator-grid-container' => count($items) >= 3,
            ])
            wire:sortable
            wire:end.stop="dispatchFormEvent('picker::moveItems', '{{ $statePath }}', $event.target.sortable.toArray())"
            style="{{ count($items) === 1 ? '--grid-column-count: 1' : '' }}"
        >
            @foreach ($items as $uuid => $item)
                <div {{ $attributes->merge($getExtraAttributes())->class([
                    'relative block w-full overflow-hidden transition duration-75 border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white flex justify-center checkered',
                    'h-64' => ! str($item['type'])->contains('video'),
                    'md:flex-1 ' => count($items) <= 3,
                ]) }}
                    wire:sortable.item="{{ $uuid }}"
                >

                    @if (str($item['type'])->contains('image'))
                        <img
                            src="{{ $item['url'] }}"
                            alt="{{ $item['alt'] }}"
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
                        @if (count($items) > 1)
                            <button
                                title="{{ __('forms::components.repeater.buttons.move_item.label') }}"
                                x-on:click.stop
                                x-tooltip.raw="{{ __('curator::views.picker.reorder') }}"
                                wire:sortable.handle
                                type="button"
                                class="flex items-center justify-center flex-none w-10 h-10 transition text-gray-400 hover:text-gray-300 cursor-grab"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" width="32" height="32" viewBox="0 0 24 24"><path fill="currentColor" d="m22.67 12l-4.49 4.5l-2.51-2.5l1.98-2l-1.98-1.96l2.51-2.51L22.67 12M12 1.33l4.47 4.49l-2.51 2.51L12 6.35l-2 1.98l-2.5-2.51L12 1.33m0 21.34l-4.47-4.49l2.51-2.51L12 17.65l2-1.98l2.5 2.51l-4.5 4.49M1.33 12l4.49-4.5L8.33 10l-1.98 2l1.98 1.96l-2.51 2.51L1.33 12M12 10a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z"/></svg>
                                <span class="sr-only">
                                    {{ __('forms::components.repeater.buttons.move_item.label') }}
                                </span>
                            </button>
                        @endif

                        <x-filament::dropdown placement="bottom-end" :teleport="true">
                            <x-slot name="trigger">
                                <button
                                    title="{{ __('forms::components.repeater.buttons.item_options.label') }}"
                                    type="button"
                                    class="flex items-center justify-center flex-none w-10 h-10 transition text-gray-400 hover:text-gray-300"
                                >
                                    @svg('heroicon-s-dots-vertical', 'w-4 h-4')
                                    <span class="sr-only">
                                        {{ __('forms::components.repeater.buttons.item_options.label') }}
                                    </span>
                                </button>
                            </x-slot>

                            <x-filament::dropdown.list>
                                <x-filament::dropdown.list.item
                                    color="secondary"
                                    icon="heroicon-s-eye"
                                    href="{{ $item['url'] }}"
                                    tag="a"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ __('curator::views.picker.view') }}
                                </x-filament::dropdown.list.item>

                                <x-filament-support::dropdown.list.item
                                    color="secondary"
                                    icon="heroicon-s-download"
                                    wire:click="mountFormComponentAction('{{ $statePath }}', 'curator_download')"
                                >
                                    {{ __('curator::views.picker.download') }}
                                </x-filament-support::dropdown.list.item>

                                @if (! $isDisabled())
                                <x-filament::dropdown.list.item
                                    color="secondary"
                                    icon="heroicon-s-pencil"
                                    href="{{ \Awcodes\Curator\Resources\MediaResource::getUrl('edit', $item['id']) }}"
                                    tag="a"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                >
                                    {{ __('curator::views.picker.edit') }}
                                </x-filament::dropdown.list.item>
                                <x-filament::dropdown.list.item
                                        color="secondary"
                                        icon="heroicon-s-minus-circle"
                                        x-on:click="close(); removeItem('{{ $uuid }}')"
                                        rel="noopener noreferrer"
                                >
                                    {{ __('curator::views.picker.remove') }}
                                </x-filament::dropdown.list.item>
                                @endif
                            </x-filament::dropdown.list>
                        </x-filament::dropdown>
                        </div>

                    </div>

                    @if (! str($item['type'])->contains('video'))
                        <div
                            class="absolute inset-x-0 bottom-0 flex items-center justify-between px-2 pt-10 pb-1 text-xs text-white bg-gradient-to-t from-black/80 to-transparent gap-3"
                        >
                            <p class="truncate">{{ $item['name'] }}.{{ $item['ext'] }}</p>
                            <p class="flex-shrink-0">{{ $item['size_for_humans'] }}</p>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        <x-filament::button
            type="button"
            color="{{ $getColor() }}"
            outlined="{{ $isOutlined() }}"
            size="{{ $getSize() }}"
            wire:click="mountFormComponentAction('{{ $statePath }}', 'curator_picker')"
            class="{{ count($items) > 0 ? 'mt-4' : '' }}"
        >
            {{ $getButtonLabel() }}
        </x-filament::button>
    </div>

</x-dynamic-component>
