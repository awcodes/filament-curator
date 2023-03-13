<div x-data="curator({statePath: '{{ $statePath }}', types: @js($acceptedFileTypes)})"
     x-on:clear-selected="selected = null"
     x-on:insert-media.window="$dispatch('close-modal', { id: '{{ $modalId }}' })"
     x-on:new-media-added.window="addNewFile($event.detail.media)"
     x-on:remove-media.window="removeFile($event.detail.media)"
     class="absolute inset-0 flex flex-col h-full curator"
>

    <div class="relative flex flex-col flex-1 overflow-hidden lg:flex-row">
        <div
            x-show="isFetching"
            class="absolute inset-0 z-10 grid place-content-center bg-gray-300/50 dark:bg-gray-900/50"
            style="display: none;"
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

        <div class="flex-1 h-full p-4 overflow-auto">
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
                                    'curator-document-image grid place-items-center w-full h-full text-xs uppercase relative',
                                ])>
                                    <template x-if="file.type.includes('video')">
                                        <div class="relative grid w-full h-full place-items-center">
                                            @svg('heroicon-o-video-camera', ['class' => 'w-16 h-16 opacity-20'])
                                            <span class="absolute block" x-text="file.ext"></span>
                                        </div>
                                    </template>
                                    <template x-if="!file.type.includes('video')">
                                        <div class="relative grid w-full h-full place-items-center">
                                            @svg('heroicon-o-document', ['class' => 'w-16 h-16 opacity-20'])
                                            <span class="absolute block" x-text="file.ext"></span>
                                        </div>
                                    </template>
                                    <span class="sr-only"><span x-text="file.name"></span></span>
                                </div>
                            </template>
                        </button>

                        <p x-text="file.name" class="absolute inset-x-0 bottom-0 px-1 pt-4 pb-1 text-xs text-white truncate pointer-events-none bg-gradient-to-t from-black/80 to-transparent"></p>

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

            <label class="relative border-b border-gray-300 dark:border-gray-800">
                <span class="sr-only">{{ __('curator::views.panel.search_label') }}</span>
                @svg('heroicon-o-search', 'w-4 h-4 absolute top-3 left-3 rtl:left-0 rtl:right-3 dark:text-gray-500')
                <input
                    type="search"
                    wire:ignore
                    placeholder="{{ __('curator::views.panel.search_placeholder') }}"
                    x-on:input.debounce.500ms="searchFiles()"
                    class="block w-full pl-10 transition duration-75 border-none rtl:pl-3 rtl:pr-10 focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 dark:bg-black/10 dark:text-white"
                />
            </label>

            <div x-show="! selected" class="flex-1 overflow-hidden">
                <div class="flex flex-col h-full overflow-y-auto">
                    <h4 class="px-4 py-2 mb-0 font-bold">
                        {{ __('curator::views.panel.add_files') }}
                    </h4>

                    <div class="flex-1 px-4 pb-4 overflow-auto">
                        {{ $this->addMediaForm }}
                    </div>

                    <div class="flex items-center justify-start gap-3 px-4 py-3 bg-gray-200 border-t border-gray-300 dark:border-gray-800 dark:bg-black/10">
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

                    <h4 class="px-4 py-2 mb-0 font-bold">
                        {{ __('curator::views.panel.edit_media') }}
                    </h4>

                    <div class="flex-1 px-4 pb-4 overflow-auto">

                        <div class="relative flex justify-center flex-shrink-0 h-48 mb-4 overflow-hidden border border-gray-300 rounded dark:border-gray-700 checkered">
                            <template x-if="selected?.type.includes('image')">
                                <img
                                    x-bind:src="selected?.url"
                                    x-bind:alt="selected?.alt"
                                    x-bind:width="selected?.width"
                                    x-bind:height="selected?.height"
                                    class="block object-contain w-full h-full"
                                />
                            </template>
                            <template x-if="!selected?.type.includes('image')">
                                <div @class([
                                    'curator-document-image grid place-items-center w-full h-full text-xs uppercase relative',
                                ])>
                                    <template x-if="selected?.type.includes('video')">
                                        <video controls x-bind:src="selected?.url"></video>
                                    </template>
                                    <template x-if="!selected?.type.includes('video')">
                                        <div class="relative grid w-full h-full place-items-center">
                                            @svg('heroicon-o-document', ['class' => 'w-16 h-16 opacity-20'])
                                            <span class="absolute block" x-text="selected?.ext"></span>
                                            <span class="sr-only"><span x-text="selected?.name"></span></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <div class="absolute top-0 right-0 flex bg-gray-900 divide-x divide-gray-700 rounded-bl-lg shadow-md">
                                <a
                                    x-bind:href="selected?.url"
                                    target="_blank"
                                    rel="noopener nofollow"
                                    class="flex items-center justify-center flex-none w-10 h-10 text-gray-600 transition hover:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400"
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

                    <div class="flex items-center justify-start gap-3 px-4 py-3 bg-gray-200 border-t border-gray-300 dark:border-gray-800 dark:bg-black/10">

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
