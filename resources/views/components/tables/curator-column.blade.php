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
            @class([
                'bg-gray-200 dark:bg-gray-700',
                'rounded-full overflow-hidden grid place-content-center' => $isRounded()
            ])
        >
            @if (str($media->type)->contains('image'))
                <img
                    src="{{ $media->getSignedUrl(['w' => $width, 'h' => $height, 'fit' => 'crop', 'fm' => 'webp']) }}"
                    style="
                        {!! $height !== null ? "height: {$height};" : null !!}
                        {!! $width !== null ? "width: {$width};" : null !!}
                    "
                    @class([
                        'h-full w-auto' => str($media->type)->contains('svg'),
                        'object-cover object-center' => ! str($media->type)->contains('svg') && ($isRounded() || $width || $height)
                    ])
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
