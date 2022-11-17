<x-filament::form
    wire:submit.prevent="create"
    class="flex flex-col h-full w-full overflow-hidden"
>
    <div class="flex-1 w-full p-4 md:p-6 h-full relative overflow-auto">
        {{ $this->form }}
    </div>

    <div class="flex items-center justify-start gap-3 p-2 border-t border-gray-300 bg-gray-200 dark:border-gray-800 dark:bg-black/10">
        <x-filament::button
            type="submit"
            size="sm"
            wire:target="create"
            disabled="{{ blank($data['files']) }}"
        >
            {{ __('filament-curator::media-picker-modal.add_files') }}
        </x-filament::button>
    </div>
</x-filament::form>
