(function(){"use strict";function d(e){let t=e.detail;Array.isArray(t)&&t.length>0&&(t=t[0]);let{statePath:i,media:r}=t||{};if(!i||!r)return;let n=i.includes(".")?i.split(".").pop():i,o=Array.isArray(r)?r:[r];if(o.length===0||!o[0]||!o[0].url)return;let c=document.querySelector(`[wire\\:key*="${n}"] .tiptap`);c||(c=document.querySelector(".tiptap.ProseMirror")),c&&u(c,o[0])}function u(e,t){if(typeof Alpine>"u")return;let i=e.closest("[x-data]");if(i)try{let r=s(e,i);r&&typeof r.chain=="function"&&f(r,t)}catch{}}function s(e,t){let i=Alpine.$data(t),r=["editor","_editor","instance","tiptap","$editor"];for(let n of r)if(i[n]&&typeof i[n].chain=="function")return i[n];if(e.__vue__&&e.__vue__.editor)return e.__vue__.editor;if(e.editor)return e.editor;if(window.filamentRichEditors){let n=t.getAttribute("wire:key");if(n&&window.filamentRichEditors[n])return window.filamentRichEditors[n]}return null}function f(e,t){try{e.chain().focus().setImage({id:t.id,src:t.url,alt:t.alt||"",title:t.title||null}).run(),l()}catch{}}function l(){try{let e=document.querySelector('[wire\\:key*="curator-panel"]');if(e){let t=e.querySelector('[x-on\\:click*="close"]');t&&t.click()}}catch{}}function a(){window.addEventListener("insert-media",d)}document.readyState==="loading"?document.addEventListener("DOMContentLoaded",a):a()})();
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
