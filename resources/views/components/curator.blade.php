<div x-data="{
    selected: null,
    files: [],
    nextPageUrl: null,
    isFetching: false,
    init() {
        this.getFiles('/filament-curator/media');
        this.$el.closest('.filament-modal-window').classList.add('filament-curator-modal-window');
    },
    getFiles: async function(url = '/filament-curator/media', selected = null) {
        if (selected) {
            let indicator = url.includes('?') ? '&' : '?';
            url = url + indicator + 'media_id=' + selected;
        }
        this.isFetching = true;
        const response = await fetch(url);
        const result = await response.json();
        this.files = this.files ? this.files.concat(result.data) : result.data;
        this.nextPageUrl = result.next_page_url;
        this.isFetching = false;
    },
    loadMoreFiles: async function() {
        if (this.nextPageUrl) {
            this.isFetching = true;
            await this.getFiles(this.nextPageUrl, this.selected?.id);
            this.isFetching = false;
        }
    },
    searchFiles: async function(event) {
        this.isFetching = true;
        const response = await fetch('/filament-curator/media/search?q=' + event.target.value);
        const result = await response.json();
        this.files = result.data;
        this.isFetching = false;
    },
    addNewFile: function(media = null) {
        if (media) {
            this.files = [...media, ...this.files];
            this.$nextTick(() => {
                this.setSelected(media[0].id);
            })
        }
    },
    removeFile: function(media = null) {
        if (media) {
            this.files = this.files.filter((obj) => obj.id !== media.id);
            this.selected = null;
        }
    },
    setSelected: function(mediaId = null) {
        if (!mediaId || (this.selected && this.selected.id === mediaId)) {
            this.selected = null;
        } else {
            this.selected = this.files.find(obj => obj.id === mediaId);
        }
        this.$wire.setCurrentFile(this.selected);
    },
    resetPicker: function() {
        setTimeout(() => {
            this.files = [];
            this.setSelected();
        }, 1000);
    }
}"
     x-on:close-modal.window="$event.detail.id === '{{ $statePath }}' ? resetPicker() : null"
     x-on:clear-selected="selected = null"
     x-on:new-media-added.window="addNewFile($event.detail.media)"
     x-on:remove-media.window="removeFile($event.detail.media)"
     class="filament-curator h-full absolute inset-0 flex flex-col"
>

    <div class="flex-1 relative flex flex-col md:flex-row overflow-hidden">
        <div
            x-show="isFetching"
            x-cloak
            class="absolute inset-0 z-10 grid place-content-center bg-gray-300/50 dark:bg-gray-900/50"
        >
            <x-filament-curator::loading-icon class="w-12 h-12 text-gray-700 dark:text-white" />
        </div> <!--loading -->

        <div class="flex-1 h-full overflow-auto p-4">

            <ul class="grid grid-cols-3 gap-4 sm:grid-cols-4 md:grid-cols-6 2xl:grid-cols-8">

                <template x-for="file in files">

                    <li x-bind:key="file.id" class="relative aspect-square">

                        <button
                            type="button"
                            x-on:click.prevent="setSelected(file.id)"
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
                                <x-filament-curator::document-image icon-size="lg">
                                    <x-slot name="label"><span x-text="file.filename"></span></x-slot>
                                </x-filament-curator::document-image>
                            </template>
                        </button>

                        <p x-text="file.title ?? file.filename" class="text-xs truncate absolute bottom-0 inset-x-0 px-1 pb-1 pt-4 text-white bg-gradient-to-t from-black/80 to-transparent"></p>

                        <button
                            type="button"
                            x-on:click="setSelected()"
                            class="absolute inset-0 flex items-center justify-center w-full h-full rounded shadow text-primary-600 bg-primary-500/20 ring-2 ring-primary-500"
                            x-show="selected && selected.id === file.id"
                            x-cloak
                        >
                            <div class="flex items-center justify-center w-8 h-8 text-white rounded-full bg-primary-500 drop-shadow">
                                <x-heroicon-s-check class="w-5 h-5" />
                            </div>
                            <span class="sr-only">
                                {{ __('filament-curator::media-picker-modal.deselect') }}
                            </span>
                        </button>
                    </li>

                </template>

                <li
                    class="relative aspect-square"
                    x-intersect="loadMoreFiles();"
                    x-show="nextPageUrl"
                    x-cloak
                >
                    <button
                        type="button"
                        x-on:click.prevent="loadMoreFiles()"
                        class="absolute inset-0 flex items-center justify-center !bg-gray-700 focus:outline focus:outline-offset-1 focus:outline-2 focus:outline-primary-500 focus:shadow-lg"
                    >
                        {{ __('filament-curator::media-picker-modal.load_more') }}
                    </button>
                </li>

                <li
                    x-show="files.length === 0"
                    x-cloak
                    class="col-span-3 sm:col-span-4 md:col-span-6 lg:col-span-8"
                >
                    {{ __('filament-curator::media-picker-modal.empty_results') }}
                </li>
            </ul>
        </div> <!-- gallery -->

        <div class="w-full h-full max-w-xs overflow-auto bg-gray-100 dark:bg-gray-900/30 flex flex-col">

            <div x-show="! selected" class="flex-1 overflow-hidden">
                <div class="flex flex-col h-full overflow-y-auto">
                    <h4 class="font-bold py-2 px-4 mb-0">
                        {{ __('filament-curator::media-picker-modal.add_files') }}
                    </h4>

                    <div class="flex-1 overflow-auto px-4 pb-4">
                        {{ $this->addMediaForm }}
                    </div>

                    <div class="flex items-center justify-start gap-3 p-2 border-t border-gray-300 bg-gray-200 dark:border-gray-800 dark:bg-black/10">
                        <x-filament::button
                            type="button"
                            size="sm"
                            wire:target="addFiles"
                            wire:click.prevent="addFiles"
                        >
                            {{ __('filament-curator::media-picker-modal.add_files') }}
                        </x-filament::button>
                    </div>
                </div>
            </div> <!-- add-media-form -->

            <div x-show="selected" class="flex-1 overflow-hidden">

               <div class="flex flex-col h-full overflow-y-auto">

                    <h4 class="font-bold py-2 px-4 mb-0">
                        {{ __('filament-curator::media-picker-modal.edit_media') }}
                    </h4>

                    <div class="flex-1 overflow-auto px-4 pb-4">

                        <div class="flex justify-center mb-4 overflow-hidden border border-gray-300 rounded dark:border-gray-700 checkered h-48 flex-shrink-0 relative">
                            <template x-if="selected?.type.includes('image')">
                                <img x-bind:src="selected?.medium_url"
                                     x-bind:alt="selected?.alt"
                                     x-bind:width="selected?.width"
                                     x-bind:height="selected?.height"
                                     class="block object-cover h-full" />
                            </template>
                            <template x-if="!selected?.type.includes('image')">
                                <x-filament-curator::document-image icon-size="lg">
                                    <x-slot name="label"><span x-text="selected?.filename"></span></x-slot>
                                </x-filament-curator::document-image>
                            </template>
                            <div class="absolute top-0 right-0 flex bg-gray-900 divide-x divide-gray-700 rounded-bl-lg shadow-md">
                                <a x-bind:href="selected?.url" target="_blank" rel="noopener nofollow"
                                   class="flex items-center justify-center flex-none w-10 h-10 transition text-gray-600 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                                   x-tooltip.raw="{{ __('filament-curator::media-picker-modal.view') }}"
                                >
                                    @svg('heroicon-s-eye', 'w-4 h-4')
                                    <span class="sr-only">{{ __('filament-curator::media-picker-modal.view') }}</span>
                                </a>
                                <button
                                    type="button"
                                    wire:click="download"
                                    class="flex items-center justify-center flex-none w-10 h-10 transition text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400"
                                    x-tooltip.raw="{{ __('filament-curator::media-picker-modal.download') }}"
                                >
                                    @svg('heroicon-s-download', 'w-4 h-4')
                                    <span class="sr-only">{{ __('filament-curator::media-picker-modal.download') }}</span>
                                </button>
                                <button
                                    type="button"
                                    wire:target="destroyFile"
                                    wire:click.prevent="destroyFile"
                                    x-tooltip.raw="{{ __('filament-curator::media-picker-modal.edit_delete') }}"
                                    class="flex items-center justify-center flex-none w-10 h-10 transition text-danger-600 hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400"
                                >
                                    @svg('heroicon-s-trash', 'w-4 h-4')
                                    <span class="sr-only">{{ __('filament-curator::media-picker-modal.edit_delete') }}</span>
                                </button>
                            </div>
                        </div>

                        {{ $this->editMediaForm }}
                    </div>

                    <div class="flex items-center justify-start gap-3 p-2 border-t border-gray-300 bg-gray-200 dark:border-gray-800 dark:bg-black/10">

                        <x-filament::button
                            type="button"
                            size="sm"
                            wire:target="updateFile"
                            wire:click.prevent="updateFile"
                        >
                            {{ __('filament-curator::media-picker-modal.edit_save') }}
                        </x-filament::button>

                        <x-filament::button
                            type="button"
                            color="secondary"
                            size="sm"
                            x-on:click.prevent="selected = null"
                        >
                            {{ __('filament-curator::media-picker-modal.edit_cancel') }}
                        </x-filament::button>
                    </div>
               </div>
            </div> <!-- edit-media-form -->
        </div> <!-- gallery forms -->
    </div> <!-- main area -->

    <x-filament-support::hr :dark-mode="config('filament.dark_mode')" />

    <div
        class="p-4 flex items-center justify-start gap-6"
    >
        <div x-bind:class="!selected ? 'opacity-75 pointer-events-none' : null">
            <x-filament::button
                type="submit"
                color="success"
                x-bind:disabled="!selected"
                x-on:click.prevent="$dispatch('close-modal', { id: '{{ $modalId }}' })"
                wire:click.prevent="insertMedia"
            >
                {{ __('filament-curator::media-picker-modal.use_selected_image') }}
            </x-filament::button>
        </div>

        <label class="fixed top-3 right-10">
            <span class="sr-only">{{ __('filament-curator::media-picker-modal.search_label') }}</span>
            <input
                type="search"
                wire:ignore
                placeholder="{{ __('filament-curator::media-picker-modal.search_placeholder') }}"
                x-on:input.debounce.500ms="searchFiles"
                class="block w-full py-1 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:border-gray-600"
            />
        </label>
    </div> <!-- footer -->

</div>