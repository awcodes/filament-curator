<livewire:curator-panel
    :settings="$settings"
    @insertMedia="dispatchFormEvent('curator::updateState', '{{ $settings['statePath'] }}', $event.detail); close()"
/>