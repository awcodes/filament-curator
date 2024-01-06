@php
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">

    <div
        x-data="{
            state: $wire.entangle('{{ $statePath }}'),
            insertPreview(event) {
                if (event.detail.statePath !== '{{ $statePath }}') return;
                this.state = event.detail.curation;
            }
        }"
        x-on:add-curation.window="insertPreview($event)"
        class="w-full curator-curation-form-component"
    >
        <div x-show="!state">
            {{ $getAction('open_curation_panel') }}
        </div>

        <div x-show="state" x-cloak>
            <div class="transition duration-75 overflow-hidden border border-gray-300 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:text-white grid md:grid-cols-2 gap-3">
                <div class="relative block w-full h-40 checkered p-4">
                    <img
                        x-bind:src="state?.url"
                        x-bind:width="state?.width"
                        x-bind:height="state?.height"
                        alt=""
                        class="w-full h-full object-contain"
                    />
                </div>
                <div>
                    <dl class="px-3 pb-3 md:py-3">
                        <div class="flex gap-2">
                            <dt class="font-bold">{{ trans('curator::views.curation.key') }}: </dt>
                            <dd x-text="state?.key"></dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="font-bold">{{ trans('curator::views.curation.width') }}: </dt>
                            <dd x-text="state?.width"></dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="font-bold">{{ trans('curator::views.curation.height') }}: </dt>
                            <dd x-text="state?.height"></dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="font-bold">{{ trans('curator::views.curation.format') }}: </dt>
                            <dd x-text="state?.ext"></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

</x-dynamic-component>
