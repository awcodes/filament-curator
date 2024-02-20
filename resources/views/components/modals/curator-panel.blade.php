<div
    x-data="{
        handleItemClick: function (mediaId = null, event) {
            if (! mediaId) return;

            if ($wire.isMultiple && event && event.{{ config('curator.multi_select_key') }}) {
                if (this.isSelected(mediaId)) {
                    let toRemove = Object.values($wire.selected).find(obj => obj.id == mediaId)
                    $wire.removeFromSelection(toRemove.id);
                    return;
                }

                $wire.addToSelection(mediaId);

                return;
            }

            if ($wire.selected.length === 1 && $wire.selected[0].id != mediaId) {
                $wire.removeFromSelection($wire.selected[0].id);
                $wire.addToSelection(mediaId);
                return;
            }

            if ($wire.selected.length === 1 && $wire.selected[0].id == mediaId) {
                $wire.removeFromSelection($wire.selected[0].id);
                return;
            }

            $wire.addToSelection(mediaId);
        },
        isSelected: function (mediaId = null) {
            if ($wire.selected.length === 0) return false;

            return Object.values($wire.selected).find(obj => obj.id == mediaId) !== undefined;
        },
    }"
    class="curator-panel h-full absolute inset-0 flex flex-col"
>
    <!-- Toolbar -->
    <div class="curator-panel-toolbar px-4 py-2 flex items-center justify-between bg-gray-200/70 dark:bg-black/20 dark:text-white">
        <div class="flex items-center gap-2">
            <x-filament::button
                size="xs"
                color="gray"
                x-on:click="$wire.selected = []"
                x-show="$wire.selected.length > 1"
            >
                {{ trans('curator::views.panel.deselect_all') }}
            </x-filament::button>
            @if($currentPage < $lastPage)
            <x-filament::button
                size="xs"
                color="gray"
                wire:click="loadMoreFiles()"
            >
                {{ trans('curator::views.panel.load_more') }}
            </x-filament::button>
            @endif
            @if ($isMultiple)
                @if (config('curator.multi_select_key') === 'metaKey')
                    <p class="text-xs">{{ trans('curator::views.panel.add_multiple_file', ['key' => 'Cmd']) }}</p>
                @else
                    <p class="text-xs">{{ trans('curator::views.panel.add_multiple_file', ['key' => config('curator.multi_select_key')]) }}</p>
                @endif
            @endif
        </div>
        <label class="border border-gray-300 dark:border-gray-700 rounded-md relative flex items-center">
            <span class="sr-only">{{ trans('curator::views.panel.search_label') }}</span>
            <x-filament::icon
                alias="curator::icons.check"
                icon="heroicon-s-magnifying-glass"
                class="w-4 h-4 absolute top-1.5 left-2 rtl:left-0 rtl:right-2 dark:text-gray-500"
            />
            <input
                type="search"
                placeholder="{{ trans('curator::views.panel.search_placeholder') }}"
                wire:model.live.debounce.500ms="search"
                class="block w-full transition text-sm py-1 !ps-8 !pe-3 duration-75 border-none focus:ring-1 focus:ring-inset focus:ring-primary-600 disabled:opacity-70 bg-transparent placeholder-gray-700 dark:placeholder-gray-400"
            />
            <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="animate-spin h-4 w-4 text-gray-400 dark:text-gray-500 sm absolute right-2" wire:loading.delay wire:target="search">
                <path clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill-rule="evenodd" fill="currentColor" opacity="0.2"></path>
                <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z" fill="currentColor"></path>
            </svg>
        </label>
    </div>
    <!-- End Toolbar -->

    <div class="flex-1 relative flex flex-col lg:flex-row overflow-hidden">

        <!-- Gallery -->
        <div class="curator-panel-gallery flex-1 h-full overflow-auto p-4">
            <ul class="curator-picker-grid">
                @forelse ($files as $file)
                    <li
                        wire:key="media-{{ $file['id'] }}" class="relative aspect-square"
                        x-bind:class="{
                            'opacity-40': $wire.selected.length > 0 && !isSelected('{{ $file['id'] }}')
                        }"
                    >

                        <button
                            type="button"
                            x-on:click="handleItemClick('{{ $file['id'] }}', $event)"
                            class="block w-full h-full overflow-hidden bg-gray-700 rounded-sm"
                        >
                            @if (str_contains($file['type'], 'image'))
                                <img
                                    src="{{ $file['thumbnail_url'] }}"
                                    alt="{{ $file['alt'] ?? '' }}"
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
                            {{ $file['pretty_name'] }}
                        </p>

                        <button
                            type="button"
                            x-on:click="handleItemClick('{{ $file['id'] }}', $event)"
                            x-show="isSelected('{{ $file['id'] }}')"
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
                                {{ trans('curator::views.panel.deselect') }}
                            </span>
                        </button>
                    </li>
                @empty
                    <li class="col-span-3 sm:col-span-4 md:col-span-6 lg:col-span-8">
                        {{ trans('curator::views.panel.empty') }}
                    </li>
                @endforelse
            </ul>
        </div>
        <!-- End Gallery -->

        <!-- Sidebar -->
        <div class="curator-panel-sidebar w-full lg:h-full lg:max-w-xs overflow-auto bg-gray-100 dark:bg-gray-900/30 flex flex-col shadow-top lg:shadow-none z-[1]">
            <div class="flex-1 overflow-hidden">
                <div class="flex flex-col h-full overflow-y-auto">
                    <h4 class="font-bold py-2 px-4 mb-0">
                        <span>
                            {{
                                count($selected) === 1
                                    ? trans('curator::views.panel.edit_media')
                                    : trans('curator::views.panel.add_files')
                            }}
                        </span>
                    </h4>

                    <div class="flex-1 overflow-auto px-4 pb-4">
                        <div class="h-full">
                            <div class="mb-4 mt-px">
                                {{ $this->form }}
                            </div>
                            <x-filament-actions::modals />
                        </div>
                    </div>

                    <div class="flex items-center justify-start mt-auto gap-3 py-3 px-4 border-t border-gray-300 bg-gray-200 dark:border-gray-800 dark:bg-black/10">
                        @if (count($selected) !== 1)
                            <div>
                                {{ $this->addFilesAction }}
                                {{ $this->addInsertFilesAction }}
                            </div>
                        @endif
                        @if (count($selected) === 1)
                            <div class="flex gap-3">
                                {{ $this->updateFileAction }}
                                {{ $this->cancelEditAction }}
                            </div>
                        @endif
                        @if (count($selected) > 0)
                            <div class="ml-auto">
                                {{ $this->insertMediaAction }}
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
        <!-- End Sidebar -->
    </div>
</div>
