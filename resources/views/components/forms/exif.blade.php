<div
    x-ignore
    ax-load="visible"
    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('highlighter', 'awcodes/curator') }}"
    x-data="highlighter()"
>
    <div x-ref="code" class="overflow-x-auto">{!! json_encode($getRecord()->exif, JSON_PRETTY_PRINT) !!}</div>

    <style>
        .shiki {
            padding: 1rem;
            border-radius: .5rem;
        }
    </style>
</div>