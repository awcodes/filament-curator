<livewire:curator-panel
    :settings="$settings"
    @insertMedia="callSchemaComponentMethod({{ \Illuminate\Support\Js::from($key) }}, 'updateState', $event.detail); close()"
/>