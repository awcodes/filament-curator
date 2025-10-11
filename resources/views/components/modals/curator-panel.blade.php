<livewire:curator-panel
    :settings="$settings"
    @insertMedia="callSchemaComponentMethod('{{ $key }}', 'updateState', $event.detail); close()"
/>