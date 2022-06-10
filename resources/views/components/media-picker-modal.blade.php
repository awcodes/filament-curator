<div x-data="{
    isOpen: false,
    selected: @entangle('selected'),
    fieldId: null,
    files: [],
    nextPageUrl: null,
    selectedTabId: null,
    isFetching: false,
    init() {
        this.$nextTick(() => this.selectTab(this.$id('tab', 1)));
    },
    openModal(event) {
        if (event.detail.id === 'filament-curator-media-picker') {
            this.isOpen = true;
            this.fieldId = event.detail.fieldId;
            this.getFiles('/filament-curator/media', event.detail?.mediaId || null).then(() => {
                if (event.detail?.mediaId) {
                    this.selectTab(this.$id('tab', 2));
                    this.setSelected(event.detail.mediaId);
                }
            });
        }
    },
    selectTab(id) {
        this.selectedTabId = id
    },
    isTabSelected(id) {
        return this.selectedTabId === id
    },
    whichChild(el, parent) {
        return Array.from(parent.children).indexOf(el) + 1
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
            this.files.unshift(media);
            this.$nextTick(() => {
                this.selectTab(this.$id('tab', 2));
                this.setSelected(media.id);
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
        this.files = [];
        this.setSelected(null);
        this.selectTab(this.$id('tab', 1));
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
    class="inline-block filament-curator filament-curator-media-picker-modal"
    x-id="['tab']">

    <div x-show="isOpen"
        x-transition:enter="ease duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-cloak
        class="fixed inset-0 z-40 flex items-center min-h-screen p-10 overflow-y-auto transition">

        <button x-on:click="isOpen = false; resetPicker();"
            type="button"
            aria-hidden="true"
            class="fixed inset-0 w-full h-full bg-black/50 focus:outline-none filament-curator-media-picker-modal-close-overlay"></button>

        <div x-show="isOpen"
            x-trap.noscroll="isOpen"
            x-on:keydown.window.escape="isOpen = false; resetPicker();"
            x-transition:enter="ease duration-300"
            x-transition:enter-start="translate-y-8"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="ease duration-300"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-8"
            x-cloak
            class="relative w-full h-full mt-auto cursor-pointer md:mb-auto">
            <div @class([
                'w-full mx-auto space-y-2 bg-white rounded-xl flex flex-col cursor-default h-full filament-curator-media-picker-modal-window',
                'dark:bg-gray-800' => config('filament.dark_mode'),
            ])>
                <div id="filament-curator-media-modal-heading"
                    @class([
                        'flex items-center justify-between py-2 pl-4 pr-2 border-b border-gray-300 filament-curator-media-picker-modal-header',
                        'dark:border-gray-700' => config('filament.dark_mode'),
                    ])>
                    <h3 class="font-bold">{{ __('Media Picker') }}</h3>
                    <div
                        class="flex items-center space-x-1 rtl:space-x-reverse group filament-forms-text-input-component">
                        <label for="media-search-input"
                            class="sr-only">
                            {{ __('Search') }}
                        </label>

                        <div class="flex-1">
                            <input type="search"
                                id="media-search-input"
                                wire:ignore
                                placeholder="Search"
                                x-on:input.debounce.500ms="searchFiles"
                                class="block w-full py-1 transition duration-75 border-gray-300 rounded-lg shadow-sm focus:border-primary-600 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:border-gray-600" />
                        </div>

                    </div>
                </div>

                <div class="flex-1 space-y-2 overflow-hidden filament-curator-media-picker-modal-content">
                    <div class="h-full p-4 space-y-4">
                        <div class="flex flex-col h-full"
                            wire:ignore>
                            <ul x-ref="tablist"
                                x-on:keydown.right.prevent.stop="$focus.wrap().next()"
                                x-on:keydown.home.prevent.stop="$focus.first()"
                                x-on:keydown.page-up.prevent.stop="$focus.first()"
                                x-on:keydown.left.prevent.stop="$focus.wrap().prev()"
                                x-on:keydown.end.prevent.stop="$focus.last()"
                                x-on:keydown.page-down.prevent.stop="$focus.last()"
                                role="tablist"
                                class="flex items-stretch -mb-px text-sm">
                                <li>
                                    <button :id="$id('tab', whichChild($el.parentElement, $refs.tablist))"
                                        x-on:click="selectTab($el.id); $dispatch('clear-selected');"
                                        x-on:focus="selectTab($el.id)"
                                        type="button"
                                        x-bind:tabindex="isTabSelected($el.id) ? 0 : -1"
                                        x-bind:aria-selected="isTabSelected($el.id)"
                                        x-bind:class="isTabSelected($el.id) ?
                                            'border-gray-300 dark:border-gray-700 bg-gray-200 dark:bg-gray-700' :
                                            'border-transparent'"
                                        class="inline-flex px-4 py-2 border-t border-l border-r rounded-t-md"
                                        role="tab">{{ __('Upload Media') }}</button>
                                </li>
                                <li>
                                    <button :id="$id('tab', whichChild($el.parentElement, $refs.tablist))"
                                        x-on:click="selectTab($el.id)"
                                        x-on:focus="selectTab($el.id)"
                                        type="button"
                                        x-bind:tabindex="isTabSelected($el.id) ? 0 : -1"
                                        x-bind:aria-selected="isTabSelected($el.id)"
                                        x-bind:class="isTabSelected($el.id) ?
                                            'border-gray-300 dark:border-gray-700 bg-gray-200 dark:bg-gray-700' :
                                            'border-transparent'"
                                        class="inline-flex px-4 py-2 border-t border-l border-r rounded-t-md"
                                        role="tab">{{ __('Media Library') }}</button>
                                </li>
                            </ul>

                            <div role="tabpanels"
                                class="flex-1 h-full overflow-hidden border border-gray-300 dark:border-gray-700 rounded-b-md">
                                <section x-show="isTabSelected($id('tab', whichChild($el, $el.parentElement)))"
                                    x-bind:aria-labelledby="$id('tab', whichChild($el, $el.parentElement))"
                                    role="tabpanel"
                                    class="h-full p-4 overflow-y-scroll md:p-6">
                                    @livewire('create-media-form')
                                </section>
                                <section x-show="isTabSelected($id('tab', whichChild($el, $el.parentElement)))"
                                    x-bind:aria-labelledby="$id('tab', whichChild($el, $el.parentElement))"
                                    role="tabpanel"
                                    class="h-full overflow-hidden">
                                    <div class="relative flex w-full h-full overflow-hidden">
                                        <div class="relative flex-1 h-full p-4 overflow-scroll">

                                            {{-- Loading Indicator --}}
                                            <div x-show="isFetching"
                                                style="display: none;"
                                                class="absolute inset-0 z-10 flex items-center justify-center bg-gray-300 dark:bg-gray-900 bg-opacity-70">
                                                <svg class="w-12 h-12 text-white animate-spin"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25"
                                                        cx="12"
                                                        cy="12"
                                                        r="10"
                                                        stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75"
                                                        fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </div>
                                            {{-- End Loading Indicator --}}

                                            {{-- File List --}}
                                            <ul
                                                class="grid grid-cols-3 gap-4 sm:grid-cols-4 md:grid-cols-6 2xl:grid-cols-8">
                                                <template x-for="file in files">
                                                    <li x-bind:key="file.id"
                                                        class="relative aspect-square">
                                                        <button type="button"
                                                            x-on:click.prevent="setSelected(file.id)"
                                                            class="block overflow-hidden bg-gray-700 rounded-sm">
                                                            <img x-bind:src="file.thumbnail_url"
                                                                x-bind:alt="file.alt"
                                                                width="300"
                                                                height="300"
                                                                class="block w-full h-full checkered" />
                                                        </button>
                                                        <button x-on:click="setSelected(null)"
                                                            style="display: none;"
                                                            class="absolute inset-0 flex items-center justify-center w-full h-full rounded shadow text-primary-600 bg-primary-500/20 ring-2 ring-primary-500"
                                                            x-show="selected && selected.id === file.id">
                                                            <div
                                                                class="flex items-center justify-center w-8 h-8 text-white rounded-full bg-primary-500 drop-shadow">
                                                                <x-heroicon-s-check class="w-5 h-5" />
                                                            </div>
                                                            <span class="sr-only">{{ __('Deselect') }}</span>
                                                        </button>
                                                    </li>
                                                </template>
                                                <li class="relative aspect-square"
                                                    x-intersect="loadMoreFiles();"
                                                    x-show="nextPageUrl"
                                                    style="display: none;">
                                                    <button type="button"
                                                        x-on:click.prevent="loadMoreFiles()"
                                                        class="absolute inset-0 flex items-center justify-center !bg-gray-700 focus:outline focus:outline-offset-1 focus:outline-2 focus:outline-primary-500 focus:shadow-lg">
                                                        {{ __('Load More') }}
                                                    </button>
                                                </li>
                                                <li x-show="files.length === 0"
                                                    style="display: none;"
                                                    class="col-span-3 sm:col-span-4 md:col-span-6 lg:col-span-8">
                                                    {{ __('No Files in the library or nothing found for your search.') }}
                                                </li>
                                            </ul>
                                            {{-- End File List --}}

                                        </div>

                                        {{-- Edit Form --}}
                                        <div @class([
                                            'hidden w-full h-full max-w-xs overflow-scroll bg-gray-200 lg:!block ',
                                            'dark:bg-gray-900' => config('filament.dark_mode'),
                                        ])>
                                            <form wire:submit.prevent="update"
                                                x-show="selected"
                                                class="p-4">

                                                <h4 class="mb-4 font-bold">
                                                    {{ __('Edit Media') }}
                                                </h4>

                                                <div
                                                    class="flex justify-center mb-4 overflow-hidden border border-gray-300 rounded dark:border-gray-700 checkered">
                                                    <img x-bind:src="selected?.medium_url"
                                                        x-bind:alt="selected?.alt"
                                                        x-bind:width="selected?.width"
                                                        x-bind:height="selected?.height"
                                                        class="block object-cover h-full" />
                                                </div>

                                                {{ $this->form }}

                                                <div class="flex items-center gap-3 mt-4">

                                                    <x-filament::button type="submit">
                                                        <span wire:loading>
                                                            <svg class="inline-block w-5 h-5 text-white animate-spin"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                fill="none"
                                                                viewBox="0 0 24 24">
                                                                <circle class="opacity-25"
                                                                    cx="12"
                                                                    cy="12"
                                                                    r="10"
                                                                    stroke="currentColor"
                                                                    stroke-width="4"></circle>
                                                                <path class="opacity-75"
                                                                    fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                </path>
                                                            </svg>
                                                        </span>
                                                        <span>{{ __('Save') }}</span>
                                                    </x-filament::button>

                                                    <x-filament::button type="button"
                                                        color="danger"
                                                        wire:click.prevent="destroy">
                                                        {{ __('Delete') }}
                                                    </x-filament::button>

                                                    <x-filament::button type="button"
                                                        color="secondary"
                                                        x-on:click="selected = null">
                                                        {{ __('Cancel') }}
                                                    </x-filament::button>

                                                </div>

                                            </form>
                                        </div>
                                        {{-- End Edit Form --}}
                                    </div>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>

                <div @class([
                    'flex items-center justify-end p-3 filament-curator-media-picker-modal-footer border-t border-gray-300',
                    'dark:border-gray-700' => config('filament.dark_mode'),
                ])>
                    <button type="button"
                        x-bind:disabled="!selected"
                        x-on:click="$dispatch('insert-media', {id: 'filament-curator-media-picker', media: selected, fieldId: fieldId}); $dispatch('close-modal', {id: 'filament-curator-media-picker', media: selected, fieldId: fieldId})"
                        class="inline-flex items-center justify-center gap-1 px-4 text-sm font-medium text-white transition-colors border border-transparent rounded-lg shadow focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset filament-button h-9 focus:ring-white bg-success-600 hover:enabled:bg-success-500 focus:bg-success-700 focus:ring-offset-success-700 disabled:opacity-70">
                        {{ __('Use Selected Image') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
