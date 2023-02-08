<div
    {{ $attributes->merge($getExtraAttributes())->class(['px-4 py-3 curator-column']) }}
>
    @php
        $height = $getHeight();
        $width = $getWidth() ?? ($isRounded() ? $height : null);
        $media = $getMedia();
    @endphp

    @if ($media)
        <div style="
                {!! $height !== null ? "height: {$height};" : null !!}
                {!! $width !== null ? "width: {$width};" : null !!}
            "
            @class(['rounded-full overflow-hidden grid place-content-center' => $isRounded()])
        >
            @if ($isImage())
                <img
                    src="/curator/{{ $media->path }}?w=50&h=50&fit=crop&fm=webp"
                    style="
                        {!! $height !== null ? "height: {$height};" : null !!}
                        {!! $width !== null ? "width: {$width};" : null !!}
                    "
                    @class(['object-cover object-center' => $isRounded()])
                    {{ $getExtraImgAttributeBag() }}
                />
            @else
                <x-curator::document-image
                    :label="$media->name"
                    icon-size="md"
                    :type="$media->type"
                />
            @endif
        </div>
    @endif
</div>
