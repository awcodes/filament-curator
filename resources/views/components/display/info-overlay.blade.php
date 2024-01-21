@props([
    'label' => null,
    'size' => 0,
])
<div class="absolute inset-x-0 bottom-0 flex items-center justify-between px-1.5 pt-10 pb-1.5 text-xs text-white bg-gradient-to-t from-black/80 to-transparent gap-3">
    <p class="truncate">{{ $label }}</p>
    <p class="flex-shrink-0">{{ curator()->sizeForHumans($size) }}</p>
</div>