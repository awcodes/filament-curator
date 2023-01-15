@php
    $statePath = $getStatePath();
@endphp

<x-forms::field-wrapper :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$statePath"
>

    <div
        x-data="{ state: $wire.entangle('{{ $statePath }}') }"
        x-on:add-curation.window="$event.detail.statePath == '{{ $statePath }}' ? state = $event.detail.curation : null"
        class="w-full curator-curation-form-component"
    >
        <x-filament::button
            type="button"
            color="{{ $getColor() }}"
            outlined="{{ $isOutlined() }}"
            size="{{ $getSize() }}"
            wire:click="mountFormComponentAction('{{ $statePath }}', 'curator_curation')"
            x-show="! state"
        >
            {{ $getButtonLabel() }}
        </x-filament::button>

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
                            <dt class="font-bold">Key: </dt>
                            <dd x-text="state?.key"></dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="font-bold">Width: </dt>
                            <dd x-text="state?.width"></dd>
                        </div>
                        <div class="flex gap-2">
                            <dt class="font-bold">Height: </dt>
                            <dd x-text="state?.height"></dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

</x-forms::field-wrapper>
