<div
{{--    x-ignore--}}
{{--    ax-load="visible"--}}
{{--    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('curator', 'awcodes/curator') }}"--}}
{{--    x-data="curator({--}}
{{--        statePath: '{{ $statePath }}',--}}
{{--        types: @js($acceptedFileTypes),--}}
{{--        initialSelection: @js($selected),--}}
{{--        isMultiple: {{ $isMultiple ? 'true' : 'false' }},--}}
{{--        directory: '{{ $isLimitedToDirectory ? $directory : null }}',--}}
{{--    })"--}}
{{--    x-on:clear-selected="selected = null"--}}
{{--    x-on:insert-media.window="$dispatch('close-modal', { id: '{{ $modalId }}' })"--}}
{{--    x-on:new-media-added.window="addNewFile($event.detail.media)"--}}
{{--    x-on:remove-media.window="removeFile($event.detail.media)"--}}
    x-data="{
        handleItemClick: function (mediaId = null, event) {
            if (! mediaId) return;

            if ($wire.isMultiple && event && event.metaKey) {
                $wire.addToSelection(mediaId);
                return;
            }

            if ($wire.selected.length === 1 && $wire.selected[0].id !== mediaId) {
                $wire.removeFromSelection($wire.selected[0].id);
                $wire.addToSelection(mediaId);
                return;
            }

            $wire.addToSelection(mediaId);
            console.log($wire.context);
        },
        isSelected: function (mediaId = null) {
            if ($wire.selected.length === 0) return false;

            return $wire.selected.find((obj) => obj.id === mediaId) !== undefined;
        },
    }"
    class="curator h-full absolute inset-0 flex flex-col"
>
    <!-- Toolbar -->
    <div class="curator-picker-toolbar px-4 py-2 flex items-center justify-between bg-gray-200/70 dark:bg-black/20 dark:text-white">
        <div class="flex items-center gap-2">
            <x-filament::button
                size="xs"
                color="gray"
                x-on:click="$wire.selected = []"
                x-show="$wire.selected.length > 1"
            >
                {{ __('curator::views.panel.deselect_all') }}
            </x-filament::button>
            <x-filament::button
                size="xs"
                color="gray"
                wire:click="loadMoreFiles()"
            >
                {{ __('curator::views.panel.load_more') }}
            </x-filament::button>
            @if ($isMultiple)
                <p class="text-xs">Cmd + Click to select multiple files.</p>
            @endif
        </div>
        <label class="border border-gray-300 dark:border-gray-700 rounded-md relative">
            <span class="sr-only">{{ __('curator::views.panel.search_label') }}</span>
            <x-filament::icon
                alias="curator::icons.check"
                icon="heroicon-s-magnifying-glass"
                class="w-4 h-4 absolute top-1.5 left-2 rtl:left-0 rtl:right-2 dark:text-gray-500"
            />
            <input
                type="search"
                placeholder="{{ __('curator::views.panel.search_placeholder') }}"
                wire:model.live.debounce.500ms="search"
                class="block w-full transition text-sm py-1 !ps-8 !pe-3 duration-75 border-none focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 bg-transparent placeholder-gray-700 dark:placeholder-gray-400"
            />
        </label>
    </div>
    <!-- End Toolbar -->

    <div class="flex-1 relative flex flex-col lg:flex-row overflow-hidden">

        <!-- Loading Indicator -->
        <div
            wire:loading="getFiles"
            class="curator-loading-indicator absolute inset-0 z-10 grid place-content-center bg-gray-300/50 dark:bg-gray-900/50"
            style="display: none;"
        >
            <svg class="w-12 h-12 text-gray-700 dark:text-white animate-spin" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
        <!-- End Loading Indicator -->

        <!-- Gallery -->
        <div class="flex-1 h-full overflow-auto p-4">
            <ul class="curator-picker-grid">
                @forelse ($files as $file)
                    <li
                        wire:key="media-{{ $file['id'] }}" class="relative aspect-square"
                        x-bind:class="{
                            'opacity-40': $wire.selected.length > 0 && !isSelected({{ $file['id'] }})
                        }"
                    >

                        <button
                            type="button"
                            x-on:click="handleItemClick({{ $file['id'] }}, $event)"
                            class="block w-full h-full overflow-hidden bg-gray-700 rounded-sm"
                        >
                            @if (str_contains($file['type'], 'image'))
                                <img
                                    src="{{ $file['thumbnail_url'] }}"
                                    alt="{{ $file['alt'] }}"
                                    width="300"
                                    height="300"
                                    class="block w-full h-full checkered"
                                />
                            @else
                                <div class="curator-document-image grid place-items-center w-full h-full text-xs uppercase relative">
                                    <div class="relative grid place-items-center w-full h-full">
                                        @if (str_contains($file['type'], 'video'))
                                            <x-filament::icon
                                                alias="curator::icons.video-camera"
                                                icon="heroicon-o-video-camera"
                                                class="w-16 h-16 opacity-20"
                                            />
                                        @else
                                            <x-filament::icon
                                                alias="curator::icons.document"
                                                icon="heroicon-o-document"
                                                class="w-16 h-16 opacity-20"
                                            />
                                        @endif
                                    </div>
                                    <span class="block absolute">{{ $file['ext'] }}</span>
                                </div>
                            @endif
                        </button>

                        <p class="text-xs truncate absolute bottom-0 inset-x-0 px-1 pb-1 pt-4 text-white bg-gradient-to-t from-black/80 to-transparent pointer-events-none">
                            {{ $file['name'] }}
                        </p>

                        <button
                            type="button"
                            wire:click="removeFromSelection({{ $file['id'] }})"
                            x-show="isSelected({{ $file['id'] }})"
                            x-cloak
                            class="absolute inset-0 flex items-center justify-center w-full h-full rounded shadow text-primary-600 bg-primary-500/20 ring-2 ring-primary-500"
                        >
                            <span class="flex items-center justify-center w-8 h-8 text-white rounded-full bg-primary-500 drop-shadow">
                                <x-filament::icon
                                    alias="curator::icons.check"
                                    icon="heroicon-s-check"
                                    class="w-5 h-5"
                                />
                            </span>
                            <span class="sr-only">
                                {{ __('curator::views.panel.deselect') }}
                            </span>
                        </button>
                    </li>
                @empty
                    <li class="col-span-3 sm:col-span-4 md:col-span-6 lg:col-span-8">
                        {{ __('curator::views.panel.empty') }}
                    </li>
                @endforelse
            </ul>
        </div>
        <!-- End Gallery -->

        <!-- Sidebar -->
        <div class="w-full lg:h-full lg:max-w-xs overflow-auto bg-gray-100 dark:bg-gray-900/30 flex flex-col shadow-top lg:shadow-none z-[1]">
            <div class="flex-1 overflow-hidden">
                <div class="flex flex-col h-full overflow-y-auto">
                    @if (count($selected) <= 1)
                    <h4 class="font-bold py-2 px-4 mb-0">
                        <span>
                            {{
                                $context === 'create'
                                    ? __('curator::views.panel.add_files')
                                    : __('curator::views.panel.edit_media')
                            }}
                        </span>
                    </h4>

                    <div class="flex-1 overflow-auto px-4 pb-4">
                        <div class="h-full">
                            {{ $this->form }}
                        </div>
                    </div>
                    @endif

                    <div class="flex items-center justify-start gap-3 py-3 px-4 border-t border-gray-300 bg-gray-200 dark:border-gray-800 dark:bg-black/10">
                        <div x-show="$wire.selected.length === 0">
                            {{ $this->addFilesAction }}
                        </div>
                        <div x-show="$wire.selected.length === 1" class="flex gap-3">
                            {{ $this->updateFileAction }}
                            {{ $this->cancelEditAction }}
                        </div>
                        <div x-show="$wire.selected.length > 0" class="ml-auto">
                            {{ $this->insertMediaAction }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- End Sidebar -->
    </div>
</div>