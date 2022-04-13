<x-forms::field-wrapper :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :hint-icon="$getHintIcon()"
    :required="$isRequired()"
    :state-path="$getStatePath()">

    <div x-data="{ state: $wire.entangle('{{ $getStatePath() }}') }"
        x-on:close-modal.window="$event.detail.fieldId == '{{ $getStatePath() }}' ? state = $event.detail.media : null"
        class="w-full">

        <div wire:ignore>
            <x-filament::button x-show="!state"
                type="button"
                :outlined="true"
                x-on:click="$dispatch('open-modal', {id: 'filament-curator-media-picker', fieldId: '{{ $getStatePath() }}'})">
                Add Media
            </x-filament::button>
        </div>

        <div x-show="state"
            style="display: none;"
            class="relative block w-full h-64 overflow-hidden transition duration-75 border border-gray-300 rounded-lg shadow-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <img x-bind:src="state?.url"
                x-bind:alt="state?.alt"
                x-bind:width="state?.width"
                x-bind:height="state?.height"
                class="object-cover h-full" />
            <button type="button"
                x-on:click="state = null"
                class="absolute flex items-center justify-center w-10 h-10 text-white rounded-full top-4 left-4 !bg-black/75">
                <x-heroicon-s-x class="w-6 h-6" />
            </button>
        </div>
    </div>

    @once
        @push('modals')
            @livewire('media-picker-modal')
        @endpush
    @endonce
</x-forms::field-wrapper>
