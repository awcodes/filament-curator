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
                @php
                    $urlBuilder = \League\Glide\Urls\UrlBuilderFactory::create('/curator/', config('app.key'));
                    $url = $urlBuilder->getUrl($media->path, ['w' => $width, 'h' => $height, 'fit' => 'crop', 'fm' => 'webp']);
                @endphp
                <img
                    src="{{ $url }}"
                    style="
                        {!! $height !== null ? "height: {$height};" : null !!}
                        {!! $width !== null ? "width: {$width};" : null !!}
                    "
                    @class(['object-cover object-center' => $isRounded() || $width || $height])
                    {{ $getExtraImgAttributeBag() }}
                />
            @else
                <x-curator::document-image
                    :label="$media->name"
                    icon-size="md"
                    :type="$media->type"
                    :extension="$media->ext"
                />
            @endif
        </div>
    @endif
</div>
