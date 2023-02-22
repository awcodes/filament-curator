<div x-data="{
        statePath: '{{ $statePath }}',
        selected: null,
        files: [],
        nextPageUrl: null,
        isFetching: false,
        init() {
            this.getFiles('/curator/media');
            const observer = new IntersectionObserver(
                ([e]) => {
                    if (e.isIntersecting) {
                        this.loadMoreFiles();
                        return;
                    }
                },
                {
                    rootMargin: '0px',
                    threshold: [0],
                }
            );
            observer.observe(this.$refs.loadMore);
        },
        getFiles: async function(url = '/curator/media', selected = null) {
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
            const response = await fetch('/curator/media/search?q=' + event.target.value);
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
    }"
     x-on:clear-selected="selected = null"
     x-on:insert-media.window="$dispatch('close-modal', { id: '{{ $modalId }}' })"
     x-on:new-media-added.window="addNewFile($event.detail.media)"
     x-on:remove-media.window="removeFile($event.detail.media)"
     class="curator h-full absolute inset-0 flex flex-col"
>

    <div class="flex-1 relative flex flex-col lg:flex-row overflow-hidden">
        <div
            x-show="isFetching"
            x-cloak
            class="absolute inset-0 z-10 grid place-content-center bg-gray-300/50 dark:bg-gray-900/50"
        >
            <svg
                class="w-12 h-12 text-gray-700 dark:text-white animate-spin"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
            >
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div> <!--loading -->

        <div class="flex-1 h-full overflow-auto p-4">
            <ul class="grid grid-cols-3 gap-4 sm:grid-cols-4 md:grid-cols-6 xl:grid-cols-8 2xl:grid-cols-10">

                <template x-for="file in files">

                    <li x-bind:key="file.id" class="relative aspect-square" x-bind:class="{'opacity-40': selected && selected.id !== file.id }">

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
                                <div @class([
                                    'curator-document-image grid place-items-center w-full h-full text-sm',
                                ])>
                                    <template x-if="file.type.includes('video')">
                                        @svg('heroicon-s-video-camera', ['class' => 'w-10 h-10'])
                                    </template>
                                    <template x-if="!file.type.includes('video')">
                                        @svg('heroicon-s-document', ['class' => 'w-10 h-10'])
                                    </template>
                                    <span class="sr-only"><span x-text="file.name"></span></span>
                                </div>
                            </template>
                        </button>

                        <p x-text="file.name" class="text-xs truncate absolute bottom-0 inset-x-0 px-1 pb-1 pt-4 text-white bg-gradient-to-t from-black/80 to-transparent pointer-events-none"></p>

                        <button
                            type="button"
                            x-on:click="setSelected()"
                            x-show="selected && selected.id === file.id"
                            x-cloak
                            class="absolute inset-0 flex items-center justify-center w-full h-full rounded shadow text-primary-600 bg-primary-500/20 ring-2 ring-primary-500"
                        >
                            <span class="flex items-center justify-center w-8 h-8 text-white rounded-full bg-primary-500 drop-shadow">
                                @svg('heroicon-s-check', 'w-5 h-5')
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

            <label class="border-b border-gray-300 dark:border-gray-800 relative">
                <span class="sr-only">{{ __('curator::views.panel.search_label') }}</span>
                @svg('heroicon-o-search', 'w-4 h-4 absolute top-3 left-3 rtl:left-0 rtl:right-3 dark:text-gray-500')
                <input
                    type="search"
                    wire:ignore
                    placeholder="{{ __('curator::views.panel.search_placeholder') }}"
                    x-on:input.debounce.500ms="searchFiles"
                    class="block w-full transition pl-10 rtl:pl-3 rtl:pr-10 duration-75 border-none focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 dark:bg-black/10 dark:text-white"
                />
            </label>

            <div x-show="! selected" class="flex-1 overflow-hidden">
                <div class="flex flex-col h-full overflow-y-auto">
                    <h4 class="font-bold py-2 px-4 mb-0">
                        {{ __('curator::views.panel.add_files') }}
                    </h4>

                    <div class="flex-1 overflow-auto px-4 pb-4">
                        {{ $this->addMediaForm }}
                    </div>

                    <div class="flex items-center justify-start gap-3 py-3 px-4 border-t border-gray-300 bg-gray-200 dark:border-gray-800 dark:bg-black/10">
                        <x-filament::button
                            type="button"
                            size="sm"
                            wire:target="addFiles"
                            wire:click.prevent="addFiles"
                        >
                            {{ __('curator::views.panel.add_files') }}
                        </x-filament::button>
                    </div>
                </div>
            </div> <!-- add-media-form -->

            <div x-show="selected" class="flex-1 overflow-hidden">

               <div class="flex flex-col h-full overflow-y-auto">

                    <h4 class="font-bold py-2 px-4 mb-0">
                        {{ __('curator::views.panel.edit_media') }}
                    </h4>

                    <div class="flex-1 overflow-auto px-4 pb-4">

                        <div class="flex justify-center mb-4 overflow-hidden border border-gray-300 rounded dark:border-gray-700 checkered h-48 flex-shrink-0 relative">
                            <template x-if="selected?.type.includes('image')">
                                <img
                                    x-bind:src="selected?.url"
                                    x-bind:alt="selected?.alt"
                                    x-bind:width="selected?.width"
                                    x-bind:height="selected?.height"
                                    class="block object-cover h-full"
                                />
                            </template>
                            <template x-if="!selected?.type.includes('image')">
                                <div @class([
                                    'curator-document-image grid place-items-center w-full h-full text-sm',
                                ])>
                                    <template x-if="selected?.type.includes('video')">
                                        <video controls x-bind:src="selected?.url"></video>
                                    </template>
                                    <template x-if="!selected?.type.includes('video')">
                                        @svg('heroicon-s-document', ['class' => 'w-10 h-10'])
                                        <span class="sr-only"><span x-text="selected?.name"></span></span>
                                    </template>
                                </div>
                            </template>
                            <div class="absolute top-0 right-0 flex bg-gray-900 divide-x divide-gray-700 rounded-bl-lg shadow-md">
                                <a
                                    x-bind:href="selected?.url"
                                    target="_blank"
                                    rel="noopener nofollow"
                                    class="flex items-center justify-center flex-none w-10 h-10 transition text-gray-600 hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
                                    x-tooltip.raw="{{ __('curator::views.panel.view') }}"
                                >
                                    @svg('heroicon-s-eye', 'w-4 h-4')
                                    <span class="sr-only">{{ __('curator::views.panel.view') }}</span>
                                </a>
                                <button
                                    type="button"
                                    wire:click="download"
                                    class="flex items-center justify-center flex-none w-10 h-10 transition text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400"
                                    x-tooltip.raw="{{ __('curator::views.panel.download') }}"
                                >
                                    @svg('heroicon-s-download', 'w-4 h-4')
                                    <span class="sr-only">{{ __('curator::views.panel.download') }}</span>
                                </button>
                                <button
                                    type="button"
                                    wire:target="destroyFile"
                                    wire:click.prevent="destroyFile"
                                    x-tooltip.raw="{{ __('curator::views.panel.edit_delete') }}"
                                    class="flex items-center justify-center flex-none w-10 h-10 transition text-danger-600 hover:text-danger-500 dark:text-danger-500 dark:hover:text-danger-400"
                                >
                                    @svg('heroicon-s-trash', 'w-4 h-4')
                                    <span class="sr-only">{{ __('curator::views.panel.edit_delete') }}</span>
                                </button>
                            </div>
                        </div>

                        {{ $this->editMediaForm }}
                    </div>

                    <div class="flex items-center justify-start gap-3 py-3 px-4 border-t border-gray-300 bg-gray-200 dark:border-gray-800 dark:bg-black/10">

                        <x-filament::button
                            type="button"
                            size="sm"
                            wire:target="updateFile"
                            wire:click.prevent="updateFile"
                        >
                            {{ __('curator::views.panel.edit_save') }}
                        </x-filament::button>

                        <x-filament::button
                            type="button"
                            color="secondary"
                            size="sm"
                            x-on:click.prevent="selected = null"
                        >
                            {{ __('curator::views.panel.edit_cancel') }}
                        </x-filament::button>

                            <x-filament::button
                                type="submit"
                                color="success"
                                size="sm"
                                wire:click.prevent="insertMedia"
                                class="ml-auto"
                            >
                                {{ __('curator::views.panel.use_selected_image') }}
                            </x-filament::button>
                    </div>
               </div>
            </div> <!-- edit-media-form -->
        </div> <!-- gallery forms -->
    </div> <!-- main area -->
</div>
