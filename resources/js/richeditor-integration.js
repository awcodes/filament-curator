/**
 * Filament Curator - RichEditor Integration for v4
 *
 * This script bridges Curator's media picker with Filament v4's RichEditor (TipTap).
 * It listens for the 'insert-media' event from Curator and inserts selected media
 * into the RichEditor as images.
 *
 * @version 1.0.0
 * @license MIT
 */

(function() {
    'use strict';

    /**
     * Handle media insertion from Curator into RichEditor
     * @param {CustomEvent} event - The insert-media event
     */
    function handleMediaInsertion(event) {
        // Handle Livewire v3 dispatch format - event.detail might be wrapped in an array
        let eventData = event.detail;
        if (Array.isArray(eventData) && eventData.length > 0) {
            eventData = eventData[0];
        }

        const { statePath, media } = eventData || {};

        // Validate event data
        if (!statePath || !media) {
            return;
        }

        // Extract the field name from statePath (e.g., 'data.content' -> 'content')
        const fieldName = statePath.includes('.') ? statePath.split('.').pop() : statePath;

        // Handle both array and single media object
        const mediaArray = Array.isArray(media) ? media : [media];

        if (mediaArray.length === 0 || !mediaArray[0] || !mediaArray[0].url) {
            return;
        }

        // Find the RichEditor component by field name
        let editorElement = document.querySelector(`[wire\\:key*="${fieldName}"] .tiptap`);

        // Fallback to first available TipTap editor
        if (!editorElement) {
            editorElement = document.querySelector('.tiptap.ProseMirror');
        }

        if (editorElement) {
            insertImageIntoEditor(editorElement, mediaArray[0]);
        }
    }

    /**
     * Insert image into TipTap editor
     * @param {HTMLElement} editorElement - The TipTap editor DOM element
     * @param {Object} media - The media object containing url, alt, title, etc.
     */
    function insertImageIntoEditor(editorElement, media) {
        if (typeof Alpine === 'undefined') {
            return;
        }

        const alpineComponent = editorElement.closest('[x-data]');
        if (!alpineComponent) {
            return;
        }

        try {
            const editor = findEditor(editorElement, alpineComponent);

            if (editor && typeof editor.chain === 'function') {
                insertWithEditor(editor, media);
            }
        } catch (error) {
            // Silently fail
        }
    }

    /**
     * Find the TipTap editor instance using multiple strategies
     * @param {HTMLElement} editorElement - The TipTap editor DOM element
     * @param {HTMLElement} alpineComponent - The Alpine component element
     * @returns {Object|null} - The TipTap editor instance or null
     */
    function findEditor(editorElement, alpineComponent) {
        const alpineData = Alpine.$data(alpineComponent);

        // Strategy 1: Check Alpine data properties
        const possibleProps = ['editor', '_editor', 'instance', 'tiptap', '$editor'];
        for (const prop of possibleProps) {
            if (alpineData[prop] && typeof alpineData[prop].chain === 'function') {
                return alpineData[prop];
            }
        }

        // Strategy 2: Check Vue instance
        if (editorElement.__vue__ && editorElement.__vue__.editor) {
            return editorElement.__vue__.editor;
        }

        // Strategy 3: Check element property
        if (editorElement.editor) {
            return editorElement.editor;
        }

        // Strategy 4: Check global registry
        if (window.filamentRichEditors) {
            const editorId = alpineComponent.getAttribute('wire:key');
            if (editorId && window.filamentRichEditors[editorId]) {
                return window.filamentRichEditors[editorId];
            }
        }

        return null;
    }

    /**
     * Insert image using TipTap editor instance
     * @param {Object} editor - The TipTap editor instance
     * @param {Object} media - The media object
     */
    function insertWithEditor(editor, media) {
        try {
            editor
                .chain()
                .focus()
                .setImage({
                    src: media.url,
                    alt: media.alt || media.name || '',
                    title: media.title || media.name || '',
                })
                .run();

            closeModal();
        } catch (error) {
            // Silently fail
        }
    }

    /**
     * Close Curator modal after successful insertion
     */
    function closeModal() {
        try {
            const modal = document.querySelector('[wire\\:key*="curator-panel"]');
            if (modal) {
                const closeButton = modal.querySelector('[x-on\\:click*="close"]');
                if (closeButton) {
                    closeButton.click();
                }
            }
        } catch (error) {
            // Silently fail
        }
    }

    /**
     * Initialize the event listener
     */
    function init() {
        window.addEventListener('insert-media', handleMediaInsertion);
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();