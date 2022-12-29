@php
    $currentItem = $getCurrentItem();
    $statePath = $getStatePath();
@endphp

<x-forms::field-wrapper :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$statePath"
>

    <div
        x-data="{ state: $wire.entangle('{{ $statePath }}') }"
        x-on:insert-media.window="$event.detail.statePath == '{{ $statePath }}' ? state = $event.detail.media.id : null"
        class="w-full filament-curator-media-picker"
    >

        @if (! $currentItem)
            <div>
                <x-filament::button type="button"
                    color="{{ $getColor() }}"
                    outlined="{{ $isOutlined() }}"
                    size="{{ $getSize() }}"
                    wire:click="mountFormComponentAction('{{ $statePath }}', 'filament_curator_media_picker')">
                    {{ $getButtonLabel() }}
                </x-filament::button>
            </div>
        @else
            <div
                class="relative w-full h-64 overflow-hidden transition duration-75 border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white flex justify-center checkered">

                @if (str($currentItem['type'])->contains('image'))
                    <img
                        src="{{ $currentItem['url'] }}"
                        alt="{{ $currentItem['alt'] }}"
                         @class([
                            'h-full',
                            'object-fit' => $getFitContent(),
                            'object-cover w-full' => ! $getFitContent(),
                        ])
                    />
                @else
                    <x-filament-curator::document-image
                        label="{{ $currentItem['filename'] }}"
                        icon-size="xl"
                    />
                @endif

                <div class="absolute top-0 right-0 flex bg-gray-900 divide-x divide-gray-700 rounded-bl-lg shadow-md">
                    <a
                        href="{{ $currentItem['url'] }}"
                        target="_blank"
                        rel="noopener nofollow"
                        class="flex items-center justify-center flex-none w-10 h-10 transition text-gray-600 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                        x-tooltip.raw="{{ __('filament-curator::media-picker-modal.view') }}"
                    >
                        @svg('heroicon-s-eye', 'w-4 h-4')
                        <span class="sr-only">{{ __('filament-curator::media-picker-modal.view') }}</span>
                    </a>
                    <button
                        type="button"
                        wire:click="mountFormComponentAction('{{ $statePath }}', 'filament_curator_download_media')"
                        class="flex items-center justify-center flex-none w-10 h-10 transition text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400"
                        x-tooltip.raw="{{ __('filament-curator::media-picker-modal.download') }}"
                    >
                        @svg('heroicon-s-download', 'w-4 h-4')
                        <span class="sr-only">{{ __('filament-curator::media-picker-modal.download') }}</span>
                    </button>
                    @if (! $isDisabled())
                    <a
                        href="{{ app(config('filament-curator.media_resource'))->getUrl('edit', $currentItem['id']) }}"
                        target="_blank"
                        rel="noopener nofollow"
                        class="flex items-center justify-center flex-none w-10 h-10 transition text-success-600 hover:text-success-500 dark:text-success-500 dark:hover:text-success-400"
                        x-tooltip.raw="{{ __('filament-curator::media-picker-modal.edit') }}"
                    >
                        @svg('heroicon-s-pencil', 'w-4 h-4')
                        <span class="sr-only">{{ __('filament-curator::media-picker-modal.edit') }}</span>
                    </a>
                    <button
                        type="button"
                        x-on:click="state = null"
                        class="flex items-center justify-center flex-none w-10 h-10 transition text-danger-600 hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400"
                        x-tooltip.raw="{{ __('filament-curator::media-picker-modal.remove') }}"
                    >
                        @svg('heroicon-s-minus-circle', 'w-4 h-4')
                        <span class="sr-only">{{ __('filament-curator::media-picker-modal.remove') }}</span>
                    </button>
                    @endif
                </div>
                <div
                    class="absolute inset-x-0 bottom-0 flex items-center justify-between px-4 pt-10 pb-4 text-xs text-white bg-gradient-to-t from-black/80 to-transparent"
                >
                    <p class="truncate">{{ $currentItem['filename'] }}</p>
                    <p>{{ $currentItem['size_for_humans'] }}</p>
                </div>
            </div>
        @endif
    </div>
</x-forms::field-wrapper>
