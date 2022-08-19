<x-filament::form
    wire:submit.prevent="create"
    class="flex flex-col h-full overflow-hidden w-full"
>
    <div class="flex-1 w-full p-4 md:p-6">
        {{ $this->form }}
    </div>

    <div
        @class([
            'flex items-center justify-start p-2 border-t border-gray-300',
            'dark:border-gray-700' => config('filament.dark_mode'),
        ])
    >
        <x-filament::button
            type="submit"
            wire:target="create"
        >
            {{ __('filament-curator::media-picker-modal.edit_save') }}
        </x-filament::button>
    </div>
</x-filament::form>
