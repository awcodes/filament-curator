<div
    class="curator curation h-full absolute inset-0 flex flex-col"
>
    <div
        class="flex-1 relative flex flex-col lg:flex-row overflow-hidden"
        x-ignore
        ax-load="visible"
        ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('curation', 'awcodes/curator') }}"
        x-data="curation({
            statePath: '{{ $statePath }}',
            fileName: '{{ $media->name }}',
            fileType: '{{ $media->type }}',
            presets: @js($presets),
        })"
        x-on:add-curation.window="$dispatch('close-modal', { id: '{{ $modalId }}' })"
    >

        <div class="flex-1 w-full lg:h-full overflow-auto p-4">
            <div class="h-full w-full">
                <img
                    x-ref="image"
                    src="{{ $media->url }}"
                    x-on:ready="setData()"
                    x-on:crop="updateData()"
                    class="h-full w-auto"
                />
            </div>
        </div>

        <div class="w-full h-96 lg:h-full lg:max-w-xs overflow-auto bg-gray-50 dark:bg-gray-950/30 flex flex-col shadow-top lg:shadow-none z-[1]">
            <div class="flex-1 overflow-hidden">
                <div class="flex flex-col h-full overflow-y-auto">
                    <h2 class="font-bold py-2 px-4 mb-0">
                        {{ trans('curator::views.curation.adjustments') }}
                    </h2>

                    <div class="flex-1 overflow-auto px-4 pb-4">
                        <div class="space-y-3">
                            <div x-show="presets" x-cloak>
                                <x-curator::curation-select prefix="Preset" name="preset" x-model="preset">
                                    <option value="custom">{{ trans('curator::views.curation.custom') }}</option>
                                    <template x-for="preset in presets">
                                        <option x-bind:value="preset.key" x-bind:key="preset.key" x-text="preset.label"></option>
                                    </template>
                                </x-curator::curation-select>
                            </div>
                            <div>
                                <label
                                    class="flex items-center w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-800 text-sm"
                                    x-bind:class="{'border-danger-600 ring-danger-600 dark:border-danger-400 dark:ring-danger-400' : key === null}"
                                >
                                    <span class="w-20 flex-shrink-0 self-stretch flex items-center justify-center px-2">{{ trans('curator::views.curation.key') }}</span>
                                    <input
                                        type="text"
                                        name="key"
                                        x-model="key"
                                        class="text-sm block w-full transition duration-75 border-none focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500 !rounded-r-lg"
                                    />
                                </label>
                                <p class="text-xs mt-1 pl-2">{{ trans('curator::views.curation.key_helper') }}</p>
                            </div>
                            <x-curator::curation-select prefix="{{ trans('curator::views.curation.format') }}" name="format" x-model="format">
                                @foreach ($formats as $format)
                                    <option value="{{ $format }}">{{ $format }}</option>
                                @endforeach
                            </x-curator::curation-select>
                            <x-curator::curation-input
                                type="number"
                                prefix="{{ trans('curator::views.curation.quality') }}"
                                name="quality" x-model="quality"
                            />
                            <x-curator::curation-input
                                type="text" prefix="X" suffix="px" name="x"
                                x-on:input="setCropBoxX($event)"
                                x-bind:value="Math.round(data.x)"
                            />
                            <x-curator::curation-input
                                type="text"
                                prefix="Y"
                                suffix="px"
                                name="y"
                                x-on:input="setCropBoxY($event)"
                                x-bind:value="Math.round(data.y)"
                            />
                            <x-curator::curation-input
                                type="text"
                                prefix="{{ trans('curator::views.curation.width') }}"
                                suffix="px"
                                name="width"
                                x-on:input="setCropBoxWidth($event)"
                                x-bind:value="Math.round(cropBoxData.width)"
                            />
                            <x-curator::curation-input
                                type="text"
                                prefix="{{ trans('curator::views.curation.height') }}"
                                suffix="px" name="height" x-on:input="setCropBoxHeight($event)"
                                x-bind:value="Math.round(cropBoxData.height)"
                            />
                            <x-curator::curation-input
                                type="text"
                                prefix="{{ trans('curator::views.curation.rotate') }}"
                                suffix="{{ trans('curator::views.curation.rotate_deg') }}"
                                name="rotate" x-on:input="cropper.rotateTo($event.target.value)"
                                x-bind:value="data.rotate"
                            />
                        </div>

                        <div class="flex items-center w-full mt-3">
                            <x-filament::button
                                type="button"
                                size="sm"
                                color="gray"
                                class="!rounded-l-lg !rounded-r-none flex-1"
                                x-on:click="cropper.setAspectRatio(16/9)"
                            >
                                16:9
                            </x-filament::button>
                            <x-filament::button
                                type="button"
                                size="sm"
                                color="gray"
                                class="!rounded-none flex-1"
                                x-on:click="cropper.setAspectRatio(4/3)"
                            >
                                4:3
                            </x-filament::button>
                            <x-filament::button
                                type="button"
                                size="sm"
                                color="gray"
                                class="!rounded-none flex-1"
                                x-on:click="cropper.setAspectRatio(1)"
                            >
                                1:1
                            </x-filament::button>
                            <x-filament::button
                                type="button"
                                size="sm"
                                color="gray"
                                class="!rounded-r-lg !rounded-l-none flex-1"
                                x-on:click="cropper.setAspectRatio(2/3)"
                            >
                                2:3
                            </x-filament::button>
                        </div>

                        <div class="flex items-center w-full mt-3">
                            <x-filament::button
                                type="button"
                                size="sm"
                                color="gray"
                                class="!rounded-l-lg !rounded-r-none flex-1"
                                x-on:click="cropper.zoom(0.1)"
                                x-tooltip.raw="{{ trans('curator::views.curation.zoom_in') }}"
                            >
                                <span class="sr-only">{{ trans('curator::views.curation.zoom_in') }}</span>
                                <x-filament::icon
                                    alias="curator::icons.zoom_in"
                                    icon="heroicon-o-magnifying-glass-plus"
                                    class="w-4 h-4"
                                />
                            </x-filament::button>
                            <x-filament::button
                                type="button"
                                size="sm"
                                color="gray"
                                class="!rounded-r-lg !rounded-l-none flex-1"
                                x-on:click="cropper.zoom(-0.1)"
                                x-tooltip.raw="{{ trans('curator::views.curation.zoom_out') }}"
                            >
                                <span class="sr-only">{{ trans('curator::views.curation.zoom_out') }}</span>
                                <x-filament::icon
                                    alias="curator::icons.zoom_out"
                                    icon="heroicon-o-magnifying-glass-minus"
                                    class="w-4 h-4"
                                />
                            </x-filament::button>
                        </div>

                        <div class="flex items-center w-full mt-3">
                            <x-filament::button
                                type="button"
                                size="sm"
                                color="gray"
                                class="!rounded-l-lg !rounded-r-none flex-1"
                                x-on:click="flipHorizontally"
                                x-tooltip.raw="{{ trans('curator::views.curation.flip_horizontally') }}"
                            >
                                <span class="sr-only">{{ trans('curator::views.curation.flip_horizontally') }}</span>
                                <svg class="w-4 h-4" viewBox="0 0 24 24">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round"
                                          stroke-linejoin="round" stroke-width="2"
                                          d="m17 3l-5 5l-5-5h10m0 18l-5-5l-5 5h10M4 12H2m8 0H8m8 0h-2m8 0h-2"/>
                                </svg>
                            </x-filament::button>
                            <x-filament::button
                                type="button"
                                size="sm"
                                color="gray"
                                class="!rounded-r-lg !rounded-l-none flex-1" x-on:click="flipVertically"
                                x-tooltip.raw="{{ trans('curator::views.curation.flip_vertically') }}"
                            >
                                <span class="sr-only">{{ trans('curator::views.curation.flip_vertically') }}</span>
                                <svg class="w-4 h-4" viewBox="0 0 24 24">
                                    <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m3 7l5 5l-5 5V7m18 0l-5 5l5 5V7m-9 13v2m0-8v2m0-8v2m0-8v2"/>
                                </svg>
                            </x-filament::button>
                        </div>

                        <div class="flex items-center w-full mt-3">
                            <x-filament::button
                                type="button"
                                size="sm"
                                color="gray"
                                class="!rounded-l-lg !rounded-r-none flex-1"
                                x-on:click="cropper.setDragMode('move')"
                                x-tooltip.raw="{{ trans('curator::views.curation.drag_mode') }}"
                            >
                                <span class="sr-only">{{ trans('curator::views.curation.drag_mode') }}</span>
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M13 6v5h5V7.75L22.25 12L18 16.25V13h-5v5h3.25L12 22.25L7.75 18H11v-5H6v3.25L1.75 12L6 7.75V11h5V6H7.75L12 1.75L16.25 6H13Z"/>
                                </svg>
                            </x-filament::button>
                            <x-filament::button
                                type="button"
                                size="sm"
                                color="gray"
                                class="!rounded-r-lg !rounded-l-none flex-1"
                                x-on:click="cropper.setDragMode('crop')"
                                x-tooltip.raw="{{ trans('curator::views.curation.crop_mode') }}"
                            >
                                <span class="sr-only">{{ trans('curator::views.curation.crop_mode') }}</span>
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M17 23v-4H7q-.825 0-1.412-.587Q5 17.825 5 17V7H1V5h4V1h2v16h16v2h-4v4Zm0-8V7H9V5h8q.825 0 1.413.588Q19 6.175 19 7v8Z"/>
                                </svg>
                            </x-filament::button>
                        </div>

                        <div class="flex items-center w-full mt-3">
                            <x-filament::button
                                type="button"
                                size="sm"
                                color="gray"
                                class="flex-1"
                                x-on:click="cropper.reset()"
                            >
                                {{ trans('curator::views.curation.reset') }}
                            </x-filament::button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 py-3 px-4 border-t border-gray-300 bg-gray-200 dark:border-gray-800 dark:bg-black/10">
                        <x-filament::button
                            type="button"
                            size="sm"
                            color="gray"
                            wire:click="$dispatch('close-modal', { id: '{{ $modalId }}' })"
                        >
                            {{ trans('curator::views.curation.cancel') }}
                        </x-filament::button>
                        <x-filament::button
                            type="button"
                            size="sm"
                            color="success"
                            x-on:click="saveCuration()"
                            wire:target="saveCuration"
                        >
                            {{ trans('curator::views.curation.save_curation') }}
                        </x-filament::button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
