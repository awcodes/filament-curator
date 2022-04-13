<x-filament::form wire:submit.prevent="create">

    {{ $this->form }}

    <div class="flex justify-center">
        <x-filament::button class="mt-4"
            type="submit"
            size="lg">
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
            <span>Save</span>
        </x-filament::button>
    </div>
</x-filament::form>
