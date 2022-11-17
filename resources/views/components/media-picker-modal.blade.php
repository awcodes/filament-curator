<div x-data="{
    isOpen: false,
    selected: @entangle('selected'),
    statePath: null,
    files: [],
    nextPageUrl: null,
    isFetching: false,
    openModal(event) {
        if (event.detail.id === 'filament-curator') {
            this.isOpen = true;
            this.statePath = event.detail?.statePath;
            this.getFiles('/filament-curator/media', event.detail?.mediaId || null).then(() => {
                if (event.detail?.mediaId) {
                    this.setSelected(event.detail.mediaId);
                }
            });
        }
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
    },
    resetPicker: function() {
        setTimeout(() => {
            this.files = [];
            this.setSelected(null);
        }, 1000);
    }
}"
    x-on:close-modal.window="if ($event.detail.id === 'filament-curator-media-picker') isOpen = false; resetPicker();"
    x-on:open-modal.window="openModal($event)"
    x-on:clear-selected="selected = null"
    x-on:new-media-added.window="addNewFile($event.detail.media)"
    x-on:remove-media.window="removeFile($event.detail.media)"
    aria-labelledby="filament-curator-media-modal-heading"
    role="dialog"
    aria-modal="true"
    class="filament-curator filament-curator-media-picker-modal block"
>

    <div x-show="isOpen"
        x-transition:enter="ease duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-40 flex items-center min-h-screen overflow-y-auto transition"
    >

        <div x-show="isOpen"
            x-trap.noscroll="isOpen"
            x-on:keydown.window.escape="isOpen = false; resetPicker();"
            x-cloak
            class="relative w-full h-full mt-auto cursor-pointer md:mb-auto"
        >

            <div class="filament-curator-media-picker-modal-window w-full mx-auto bg-white flex flex-col cursor-default h-full dark:bg-gray-800">

                <div class="filament-curator-media-picker-modal-header flex items-center justify-between py-2 pl-4 pr-2 border-b border-gray-300 dark:border-gray-700">

                    <h3 class="text-xl font-bold tracking-tight">{{ __('filament-curator::media-picker-modal.heading') }}</h3>

                    <div class="flex items-center space-x-1 rtl:space-x-reverse group">

                        <div class="flex-1">
                            <label>
                                <span class="sr-only">{{ __('filament-curator::media-picker-modal.search_label') }}</span>
                                <input
                                    type="search"
                                    wire:ignore
                                    placeholder="{{ __('filament-curator::media-picker-modal.search_placeholder') }}"
                                    x-on:input.debounce.500ms="searchFiles"
                                    class="block w-full py-1 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                />
                            </label>
                        </div>

                        <x-filament-support::icon-button
                            icon="heroicon-s-x"
                            x-on:click="$dispatch('close-modal', {id: 'filament-curator-media-picker'})"
                        >
                            {{ __('filament-curator::media-picker-modal.edit_cancel') }}
                        </x-filament-support::icon-button>

                    </div>

                </div>

                <div class="filament-curator-media-picker-modal-content flex-1 overflow-hidden">

                    <div class="flex flex-col h-full" wire:ignore>

                            <div class="relative flex flex-1 w-full h-full overflow-hidden">

                                <div class="relative flex-1 h-full overflow-auto p-4">

                                    <div
                                        x-show="isFetching"
                                        style="display: none;"
                                        class="filament-curator-loading-spinner absolute inset-0 z-10 flex items-center justify-center bg-gray-300 dark:bg-gray-900 bg-opacity-70"
                                    >
                                        <x-filament-curator::loading-icon class="w-12 h-12 text-white" />
                                    </div>

                                    {{-- File List --}}
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
                                                            <x-slot name="label"><span
                                                                    x-text="file.filename"></span></x-slot>
                                                        </x-filament-curator::document-image>
                                                    </template>
                                                </button>

                                                <button
                                                    x-on:click="setSelected(null)"
                                                    style="display: none;"
                                                    class="absolute inset-0 flex items-center justify-center w-full h-full rounded shadow text-primary-600 bg-primary-500/20 ring-2 ring-primary-500"
                                                    x-show="selected && selected.id === file.id"
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
                                            style="display: none;"
                                        >
                                            <button type="button"
                                                x-on:click.prevent="loadMoreFiles()"
                                                class="absolute inset-0 flex items-center justify-center !bg-gray-700 focus:outline focus:outline-offset-1 focus:outline-2 focus:outline-primary-500 focus:shadow-lg">
                                                {{ __('filament-curator::media-picker-modal.load_more') }}
                                            </button>
                                        </li>

                                        <li
                                            x-show="files.length === 0"
                                            style="display: none;"
                                            class="col-span-3 sm:col-span-4 md:col-span-6 lg:col-span-8"
                                        >
                                            {{ __('filament-curator::media-picker-modal.empty_results') }}
                                        </li>
                                    </ul>
                                    {{-- End File List --}}

                                </div>

                                <div class="hidden w-full h-full max-w-xs overflow-auto bg-gray-100 lg:!block dark:bg-gray-900">
                                    <div x-show="! selected" class="h-full">
                                        @livewire('filament-curator-create-media-form')
                                    </div>

                                    <div x-show="selected" class="h-full">

                                        <form wire:submit.prevent="update" class="flex flex-col h-full">

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
                                                            class="flex items-center justify-center flex-none w-10 h-10 transition text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400"
                                                            x-tooltip="'View'"
                                                        >
                                                            @svg('heroicon-s-eye', 'w-4 h-4')
                                                        </a>
                                                        <button type="button"
                                                            wire:click="download"
                                                            class="flex items-center justify-center flex-none w-10 h-10 transition text-primary-600 hover:text-primary-500 dark:text-primary-500 dark:hover:text-primary-400"
                                                            x-tooltip="'Download'"
                                                        >
                                                            @svg('heroicon-s-download', 'w-4 h-4')
                                                        </button>
                                                    </div>
                                                </div>

                                                {{ $this->form }}
                                            </div>

                                            <div class="flex items-center justify-center gap-3 p-2 border-t border-gray-300 bg-gray-200 dark:border-gray-800 dark:bg-black/10">

                                                <x-filament::button
                                                    type="submit"
                                                    wire:target="update"
                                                    size="sm"
                                                >
                                                    {{ __('filament-curator::media-picker-modal.edit_save') }}
                                                </x-filament::button>

                                                <x-filament::button
                                                    type="button"
                                                    color="secondary"
                                                    x-on:click="selected = null"
                                                    size="sm"
                                                >
                                                    {{ __('filament-curator::media-picker-modal.edit_cancel') }}
                                                </x-filament::button>

                                                <x-filament::icon-button
                                                    type="button"
                                                    color="danger"
                                                    size="sm"
                                                    icon="heroicon-s-trash"
                                                    class="ml-auto"
                                                    wire:target="destroy"
                                                    wire:click.prevent="destroy"
                                                >
                                                    {{ __('filament-curator::media-picker-modal.edit_delete') }}

                                                </x-filament::icon-button>

                                            </div>

                                        </form>
                                    </div>
                                </div>

                            </div>

                        </div>

                </div>

                <div
                    class="filament-curator-media-picker-modal-footer p-4 flex items-center justify-start border-t border-gray-300 dark:border-gray-700"
                    x-bind:class="!selected ? 'opacity-75 pointer-events-none' : null"
                >

                    <x-filament::button
                        type="button"
                        color="success"
                        x-bind:disabled="!selected"
                        x-on:click="
                            $dispatch('insert-media', {id: 'filament-curator-media-picker', media: selected, statePath: statePath});
                            $dispatch('close-modal', {id: 'filament-curator-media-picker'});
                        "
                    >
                        {{ __('filament-curator::media-picker-modal.use_selected_image') }}
                    </x-filament::button>

                </div>

            </div>

        </div>

    </div>

</div>
