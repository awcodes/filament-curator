<div
    x-ignore
    ax-load
    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('curator', 'awcodes/curator') }}"
    x-data="curator({
        statePath: '{{ $statePath }}',
        types: @js($acceptedFileTypes),
        initialSelection: @js($selected),
        isMultiple: {{ $isMultiple ? 'true' : 'false' }},
        directory: '{{ $isLimitedToDirectory ? $directory : null }}',
    })"
    x-on:clear-selected="selected = null"
    x-on:insert-media.window="$dispatch('close-modal', { id: '{{ $modalId }}' })"
    x-on:new-media-added.window="addNewFile($event.detail.media)"
    x-on:remove-media.window="removeFile($event.detail.media)"
    class="curator h-full absolute inset-0 flex flex-col"
    wire:ignore
>
    <div class="curator-picker-toolbar px-4 py-2 flex items-center justify-between bg-gray-200/70 dark:bg-black/20 dark:text-white">
        <div class="flex items-center gap-2">
            <x-filament::button
                size="xs"
                color="gray"
                x-on:click="selected = []"
                x-show="selected.length > 1"
            >
                {{ __('curator::views.panel.deselect_all') }}
            </x-filament::button>
            @if ($isMultiple)
                <p class="text-xs">Cmd + Click to select multiple files.</p>
            @endif
        </div>
        <label class="border border-gray-300 dark:border-gray-700 rounded-md relative">
            <span class="sr-only">{{ __('curator::views.panel.search_label') }}</span>
            <x-filament::icon
                alias="curator::icons.check"
                name="heroicon-s-magnifying-glass"
                size="w-4 h-4"
                class="absolute top-1.5 left-2 rtl:left-0 rtl:right-2 dark:text-gray-500"
            />
            <input
                type="search"
                wire:ignore
                placeholder="{{ __('curator::views.panel.search_placeholder') }}"
                x-on:input.debounce.500ms="searchFiles($event)"
                class="block w-full transition text-sm py-1 !ps-8 !pe-3 duration-75 border-none focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 bg-transparent placeholder-gray-700 dark:placeholder-gray-400"
            />
        </label>
    </div>

    <div class="flex-1 relative flex flex-col lg:flex-row overflow-hidden">
        <div
            x-show="isFetching"
            class="curator-loading-indicator absolute inset-0 z-10 grid place-content-center bg-gray-300/50 dark:bg-gray-900/50"
            style="display: none;"
        >
            <svg class="w-12 h-12 text-gray-700 dark:text-white animate-spin" xmlns="http://www.w3.org/2000/svg"
                 fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div> <!--loading -->

        <div class="flex-1 h-full overflow-auto p-4" wire:ignore>
            <ul class="curator-picker-grid">

                <template x-for="file in files">

                    <li
                        x-bind:key="file.id" class="relative aspect-square"
                        x-bind:class="{'opacity-40': selected.length > 0 && !isSelected(file.id) }"
                    >

                        <button
                            type="button"
                            x-on:click.prevent="addToSelection(file.id, $event)"
                            class="block w-full h-full overflow-hidden bg-gray-700 rounded-sm"
                        >
                            <template x-if="file.type.includes('image')">
                                <img
                                    x-bind:src="file.thumbnail_url"
                                    x-bind:alt="file.alt"
                                    width="300"
                                    height="300"
                                    class="block w-full h-full checkered"
                                />
                            </template>
                            <template x-if="!file.type.includes('image')">
                                <div class="curator-document-image grid place-items-center w-full h-full text-xs uppercase relative">
                                    <div class="relative grid place-items-center w-full h-full">
                                        <template x-if="file.type.includes('video')">
                                            @svg('heroicon-o-video-camera', ['class' => 'w-16 h-16 opacity-20'])
                                        </template>
                                        <template x-if="!file.type.includes('video')">
                                            @svg('heroicon-o-document', ['class' => 'w-16 h-16 opacity-20'])
                                        </template>
                                    </div>
                                    <span class="block absolute" x-text="file.ext"></span>
                                </div>
                            </template>
                        </button>

                        <p x-text="file.name"
                           class="text-xs truncate absolute bottom-0 inset-x-0 px-1 pb-1 pt-4 text-white bg-gradient-to-t from-black/80 to-transparent pointer-events-none"></p>

                        <button
                            type="button"
                            x-on:click="removeFromSelection(file.id)"
                            x-show="isSelected(file.id)"
                            x-cloak
                            class="absolute inset-0 flex items-center justify-center w-full h-full rounded shadow text-primary-600 bg-primary-500/20 ring-2 ring-primary-500"
                        >
                            <span class="flex items-center justify-center w-8 h-8 text-white rounded-full bg-primary-500 drop-shadow">
                                <x-filament::icon
                                    alias="curator::icons.check"
                                    name="heroicon-s-check"
                                    size="w-5 h-5"
                                />
                            </span>
                            <span class="sr-only">
                                {{ __('curator::views.panel.deselect') }}
                            </span>
                        </button>
                    </li>

                </template>

                <li
                    class="relative aspect-square"
                    x-ref="loadMore"
                    x-show="nextPageUrl"
                    x-cloak
                >
                    <button
                        type="button"
                        x-on:click.prevent="loadMoreFiles()"
                        class="flex items-center w-full h-full justify-center !bg-gray-700 focus:outline focus:outline-offset-1 focus:outline-2 focus:outline-primary-500 focus:shadow-lg"
                    >
                        {{ __('curator::views.panel.load_more') }}
                    </button>
                </li>

                <li
                    x-show="files.length === 0"
                    x-cloak
                    class="col-span-3 sm:col-span-4 md:col-span-6 lg:col-span-8"
                >
                    {{ __('curator::views.panel.empty') }}
                </li>
            </ul>
        </div> <!-- gallery -->

        <div class="w-full lg:h-full lg:max-w-xs overflow-auto bg-gray-100 dark:bg-gray-900/30 flex flex-col shadow-top lg:shadow-none z-[1]">

            <div class="flex-1 overflow-hidden">
                <div class="flex flex-col h-full overflow-y-auto">
                    <h4 x-show="showUploadForm" class="font-bold py-2 px-4 mb-0">
                        {{ __('curator::views.panel.add_files') }}
                    </h4>

                    <h4 x-show="showEditForm" class="font-bold py-2 px-4 mb-0">
                        {{ __('curator::views.panel.edit_media') }}
                    </h4>

                    <div class="flex-1 overflow-auto px-4 pb-4">
                        <div x-show="showUploadForm" class="h-full">
                            {{ $this->addMediaForm }}
                        </div>
                        <div x-show="showEditForm" class="h-full">
                            <div class="flex justify-center mb-4 overflow-hidden border border-gray-300 rounded dark:border-gray-700 checkered h-48 flex-shrink-0 relative">
                                <template x-if="selected[0]?.type.includes('image')">
                                    <img
                                        x-bind:src="selected[0]?.url"
                                        x-bind:alt="selected[0]?.alt"
                                        x-bind:width="selected[0]?.width"
                                        x-bind:height="selected[0]?.height"
                                        class="block object-contain w-full h-full"
                                    />
                                </template>
                                <template x-if="!selected[0]?.type.includes('image')">
                                    <div @class([
                                        'curator-document-image grid place-items-center w-full h-full text-xs uppercase relative',
                                    ])>
                                        <template x-if="selected[0]?.type.includes('video')">
                                            <video controls x-bind:src="selected?.url"></video>
                                        </template>
                                        <template x-if="!selected[0]?.type.includes('video')">
                                            <div class="relative grid place-items-center w-full h-full">
                                                @svg('heroicon-o-document', ['class' => 'w-16 h-16 opacity-20'])
                                                <span class="block absolute" x-text="selected[0]?.ext"></span>
                                                <span class="sr-only"><span x-text="selected[0]?.name"></span></span>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <div class="absolute top-0 right-0 flex bg-gray-900 divide-x divide-gray-700 rounded-bl-lg shadow-md">
                                    <a
                                        x-bind:href="selected[0]?.url"
                                        target="_blank"
                                        rel="noopener nofollow"
                                        class="flex items-center justify-center flex-none w-10 h-10 transition text-gray-600 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                                        x-tooltip.raw="{{ __('curator::views.panel.view') }}"
                                    >
                                        <x-filament::icon
                                            alias="curator::icons.view"
                                            name="heroicon-s-eye"
                                            size="w-4 h-4"
                                        />
                                        <span class="sr-only">{{ __('curator::views.panel.view') }}</span>
                                    </a>
                                    <button
                                        type="button"
                                        wire:click="download"
                                        class="flex items-center justify-center flex-none w-10 h-10 transition text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400"
                                        x-tooltip.raw="{{ __('curator::views.panel.download') }}"
                                    >
                                        <x-filament::icon
                                            alias="curator::icons.download"
                                            name="heroicon-s-arrow-down-tray"
                                            size="w-4 h-4"
                                        />
                                        <span class="sr-only">{{ __('curator::views.panel.download') }}</span>
                                    </button>
                                    <button
                                        type="button"
                                        wire:target="destroyFile"
                                        wire:click.prevent="destroyFile"
                                        x-tooltip.raw="{{ __('curator::views.panel.edit_delete') }}"
                                        class="flex items-center justify-center flex-none w-10 h-10 transition text-danger-600 hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400"
                                    >
                                        <x-filament::icon
                                            alias="curator::icons.trash"
                                            name="heroicon-s-trash"
                                            size="w-4 h-4"
                                        />
                                        <span class="sr-only">{{ __('curator::views.panel.edit_delete') }}</span>
                                    </button>
                                </div>
                            </div>
                            {{ $this->editMediaForm }}
                        </div>
                    </div>

                    <div class="flex items-center justify-start gap-3 py-3 px-4 border-t border-gray-300 bg-gray-200 dark:border-gray-800 dark:bg-black/10">
                        <x-filament::button
                            type="button"
                            size="sm"
                            wire:target="addFiles"
                            wire:click.prevent="addFiles"
                            x-show="showUploadForm"
                        >
                            {{ __('curator::views.panel.add_files') }}
                        </x-filament::button>

                        <x-filament::button
                            type="button"
                            size="sm"
                            wire:target="updateFile"
                            wire:click.prevent="updateFile"
                            x-show="showEditForm"
                        >
                            {{ __('curator::views.panel.edit_save') }}
                        </x-filament::button>

                        <x-filament::button
                            type="button"
                            color="secondary"
                            size="sm"
                            x-on:click.prevent="selected = []"
                            x-show="showEditForm"
                        >
                            {{ __('curator::views.panel.edit_cancel') }}
                        </x-filament::button>

                        <x-filament::button
                            type="submit"
                            color="success"
                            size="sm"
                            x-on:click.prevent="insertMedia()"
                            class="ml-auto"
                            x-show="selected.length > 0"
                        >
                            {{ __('curator::views.panel.use_selected_image') }}
                        </x-filament::button>
                    </div>
                </div>
            </div>

        </div> <!-- gallery forms -->
    </div> <!-- main area -->
</div>