<x-filament::modal id="curator-panel" width="screen" class="curator-panel" displayClasses="block">
    <x-slot name="heading">
        {{ __('curator::views.panel.heading') }}
    </x-slot>
    <livewire:curator-panel/>
</x-filament::modal>