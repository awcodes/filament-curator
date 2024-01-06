<x-filament::modal id="curator-panel" width="screen" class="curator-panel" displayClasses="block">
    <x-slot name="heading">
        {{ trans('curator::views.panel.heading') }}
    </x-slot>
    <livewire:curator-panel/>
</x-filament::modal>