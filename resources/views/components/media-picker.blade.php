<x-forms::field-wrapper :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()">

    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }"
        x-on:insert-media.window="$event.detail.fieldId == '{{ $getStatePath() }}' ? state = $event.detail.media.id : null"
        class="w-full filament-curator-media-picker">
        @php
            $currentItem = $getCurrentItem($getState());
        @endphp
        @if (!$currentItem)
            <div>
                <x-filament::button type="button"
                    color="{{ $getColor() }}"
                    outlined="{{ $isOutlined() }}"
                    size="{{ $getSize() }}"
                    x-on:click="$dispatch('open-modal', {id: 'filament-curator-media-picker', fieldId: '{{ $getStatePath() }}'})">
                    {{ $getButtonLabel() }}
                </x-filament::button>
            </div>
        @else
            <div
                class="relative block w-full h-64 overflow-hidden transition duration-75 border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white">

                @if (str($currentItem['type'])->contains('image'))
                    <img src="{{ $currentItem['url'] }}"
                        alt="{{ $currentItem['alt'] }}"
                        class="object-cover w-full h-full checkered" />
                @else
                    <x-filament-curator::document-image label="{{ $currentItem['filename'] }}"
                        icon-size="xl" />
                @endif

                <div class="absolute top-0 right-0 flex bg-gray-900 divide-x divide-gray-700 rounded-bl-lg shadow-md">
                    <a href="{{ $currentItem['url'] }}" target="_blank" rel="noopener nofollow"
                        class="flex items-center justify-center flex-none w-10 h-10 transition text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400"
                        x-tooltip="'View'"
                    >
                        <x-heroicon-s-eye class="w-4 h-4" />
                    </a>
                    <button type="button"
                        wire:click="mountFormComponentAction('{{ $getStatePath() }}', 'download')"
                        class="flex items-center justify-center flex-none w-10 h-10 transition text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400"
                        x-tooltip="'Download'"
                    >
                        <x-heroicon-s-download class="w-4 h-4" />
                    </button>
                    @if (! $isDisabled())
                    <button type="button"
                        x-on:click="$dispatch('open-modal', {id: 'filament-curator-media-picker', fieldId: '{{ $getStatePath() }}', mediaId: {{ $currentItem['id'] }} })"
                        class="flex items-center justify-center flex-none w-10 h-10 transition text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400"
                        x-tooltip="'Edit'"
                    >
                        <x-heroicon-s-pencil class="w-4 h-4" />
                    </button>
                    <button type="button"
                        x-on:click="state = null"
                        class="flex items-center justify-center flex-none w-10 h-10 transition text-danger-600 hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400"
                        x-tooltip="'Delete'"
                    >
                        <x-heroicon-s-trash class="w-4 h-4" />
                    </button>
                    @endif
                </div>
                <div
                    class="absolute inset-x-0 bottom-0 flex items-center justify-between px-4 pt-10 pb-4 text-xs text-white bg-gradient-to-t from-black/80 to-transparent">
                    <p class="truncate">{{ $currentItem['filename'] }}</p>
                    <p>{{ $currentItem['size_for_humans'] }}</p>
                </div>
            </div>
        @endif
    </div>
</x-forms::field-wrapper>
