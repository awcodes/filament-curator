<div {{ $attributes->merge($getExtraAttributes())->class(['px-4 py-3 filament-currator-tables-thumbnail-column']) }}>
    @php
        $height = $getHeight();
        $width = $getWidth() ?? ($isRounded() ? $height : null);
        $item = $getRecord();
        $path = $getImagePath();
    @endphp

    <div style="
            {!! $height !== null ? "height: {$height};" : null !!}
            {!! $width !== null ? "width: {$width};" : null !!}
        "
        @class(['rounded-full overflow-hidden' => $isRounded()])>
        @if (str($item['type'])->contains('image'))
            <img src="{{ $path }}"
                style="
                    {!! $height !== null ? "height: {$height};" : null !!}
                    {!! $width !== null ? "width: {$width};" : null !!}
                "
                @class(['object-cover object-center' => $isRounded()])
                {{ $getExtraImgAttributeBag() }}>
        @else
            <x-filament-curator::document-image label="{{ $item['filename'] }}"
                icon-size="sm" />
        @endif
    </div>
</div>
