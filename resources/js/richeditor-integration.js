/**
 * Filament Curator - RichEditor Integration
 *
 * Bridges Curator's media picker with Filament v4's RichEditor (TipTap).
 * Uses Filament's native 'run-rich-editor-commands' event system.
 */

(function() {
    'use strict';

    // Singleton flag to prevent multiple initializations
    if (window.__curatorRichEditorInitialized) {
        return;
    }
    window.__curatorRichEditorInitialized = true;

    // Prevent duplicate insertions
    let processing = false;

    function handleInsertMedia(event) {
        // Prevent concurrent processing
        if (processing) {
            return;
        }
        processing = true;

        try {
            // Extract event data (handle Livewire array wrapper)
            let data = event.detail;
            if (Array.isArray(data)) data = data[0];

            const { statePath, media } = data || {};
            if (!statePath || !media) {
                return;
            }

            // Get first media item
            const items = Array.isArray(media) ? media : [media];
            const item = items[0];
            if (!item?.url) {
                return;
            }

            // Find the RichEditor component
            const wrapper = document.querySelector('.fi-fo-rich-editor');
            if (!wrapper) {
                return;
            }

            // Find the Alpine element with x-data
            const alpineEl = wrapper.querySelector('[x-data*="richEditorFormComponent"]');
            if (!alpineEl) {
                return;
            }

            // Extract the key from x-data attribute
            const xData = alpineEl.getAttribute('x-data') || '';
            const keyMatch = xData.match(/key:\s*['"]([^'"]+)['"]/);
            const key = keyMatch ? keyMatch[1] : null;

            if (!key) {
                return;
            }

            // Get livewireId from closest wire:id element
            const wireEl = wrapper.closest('[wire\\:id]');
            const livewireId = wireEl?.getAttribute('wire:id');

            if (!livewireId) {
                return;
            }

            // Dispatch the native Filament RichEditor command event
            window.dispatchEvent(new CustomEvent('run-rich-editor-commands', {
                detail: {
                    key: key,
                    livewireId: livewireId,
                    commands: [
                        { name: 'focus' },
                        {
                            name: 'setImage',
                            arguments: [{
                                src: item.url,
                                alt: item.alt || item.title || '',
                                title: item.title || '',
                                id: item.id || null,
                            }]
                        }
                    ],
                    editorSelection: { type: 'text', anchor: 1, head: 1 }
                }
            }));

            // Close modal after a short delay
            setTimeout(() => {
                const closeBtn = document.querySelector('.fi-modal button[type="button"] svg[class*="x-mark"]')?.closest('button')
                    || document.querySelector('.fi-modal [x-on\\:click*="close"]');
                if (closeBtn) {
                    closeBtn.click();
                }
            }, 100);

        } finally {
            // Reset processing flag after delay
            setTimeout(() => { processing = false; }, 300);
        }
    }

    // Initialize once
    window.addEventListener('insert-media', handleInsertMedia);
})();
