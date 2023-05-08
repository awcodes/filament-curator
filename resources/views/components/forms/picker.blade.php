@php
    $statePath = $getStatePath();
    $items = $getState();
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
        x-data="{
            state: $wire.entangle('{{ $statePath }}'),
            removeItem: function (index) {
                this.state.splice(index, 1);
            },
        }"
        x-on:insert-media.window="$event.detail.statePath == '{{ $statePath }}' ? state.push($event.detail.media) : null"
        class="curator-media-picker w-full space-y-4"
    >
        @if (count($items) > 1)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @endif
        @foreach ($items as $item)
            <div {{ $attributes->merge($getExtraAttributes())->class([
                'relative block w-full overflow-hidden transition duration-75 border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white flex justify-center checkered',
                'h-64' => ! str($item['type'])->contains('video'),
            ]) }}>

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

                <div class="absolute top-0 right-0 flex bg-gray-900 divide-x divide-gray-700 rounded-bl-lg shadow-md">
                    <a
                        href="{{ $item['url'] }}"
                        target="_blank"
                        rel="noopener nofollow"
                        class="flex items-center justify-center flex-none w-10 h-10 transition text-gray-600 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                        x-tooltip.raw="{{ __('curator::views.picker.view') }}"
                    >
                        @svg('heroicon-s-eye', 'w-4 h-4')
                        <span class="sr-only">{{ __('curator::views.picker.view') }}</span>
                    </a>
                    <button
                        type="button"
                        wire:click="mountFormComponentAction('{{ $statePath }}', 'curator_download')"
                        class="flex items-center justify-center flex-none w-10 h-10 transition text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400"
                        x-tooltip.raw="{{ __('curator::views.picker.download') }}"
                    >
                        @svg('heroicon-s-download', 'w-4 h-4')
                        <span class="sr-only">{{ __('curator::views.picker.download') }}</span>
                    </button>
                    @if (! $isDisabled())
                    <a
                        href="{{ \Awcodes\Curator\Resources\MediaResource::getUrl('edit', $item['id']) }}"
                        target="_blank"
                        rel="noopener nofollow"
                        class="flex items-center justify-center flex-none w-10 h-10 transition text-success-600 hover:text-success-500 dark:text-success-500 dark:hover:text-success-400"
                        x-tooltip.raw="{{ __('curator::views.picker.edit') }}"
                    >
                        @svg('heroicon-s-pencil', 'w-4 h-4')
                        <span class="sr-only">{{ __('curator::views.picker.edit') }}</span>
                    </a>
                    <button
                        type="button"
                        x-on:click="removeItem({{ $loop->index }})"
                        class="flex items-center justify-center flex-none w-10 h-10 transition text-danger-600 hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400"
                        x-tooltip.raw="{{ __('curator::views.picker.remove') }}"
                    >
                        @svg('heroicon-s-minus-circle', 'w-4 h-4')
                        <span class="sr-only">{{ __('curator::views.picker.remove') }}</span>
                    </button>
                    @endif
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
        @if (count($items) > 1)
            </div>
        @endif
        <x-filament::button
                type="button"
                color="{{ $getColor() }}"
                outlined="{{ $isOutlined() }}"
                size="{{ $getSize() }}"
                wire:click="mountFormComponentAction('{{ $statePath }}', 'curator_picker')"
        >
            {{ $getButtonLabel() }}
        </x-filament::button>
    </div>

</x-forms::field-wrapper>
