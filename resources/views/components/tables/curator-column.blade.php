@php
    $items = $getMedia();
    $overlap = $getOverlap() ?? 'sm';
    $imageCount = count($items);
    $ring = match ($getRing()) {
        0 => 'ring-0',
        1 => 'ring-1',
        2 => 'ring-2',
        4 => 'ring-4',
        default => 'ring',
    };

    $ringClasses = \Illuminate\Support\Arr::toCssClasses([
        'ring-white dark:ring-gray-900',
        match ($ring) {
            0 => null,
            1 => 'ring-1',
            2 => 'ring-2',
            3 => 'ring',
            4 => 'ring-4',
            default => $ring,
        },
    ]);

    $overlap = match ($overlap) {
        0 => 'space-x-0',
        2 => '-space-x-2',
        3 => '-space-x-3',
        4 => '-space-x-4',
        default => '-space-x-1',
    };

    $defaultImageUrl = $getDefaultImageUrl();

    $resolution = $getResolution();

    $height = $getHeight();
    $width = $getWidth() ?? ($isRounded() ? $height : null);
@endphp

<div
    {{ $attributes->merge($getExtraAttributes())->class([
        'curator-column px-4 py-3',
        $overlap . ' flex items-center' => $imageCount > 1,
    ]) }}
>
    @if ($items)
        @foreach ($items as $item)
        <div style="
                {!! $height !== null ? "height: {$height};" : null !!}
                {!! $width !== null ? "width: {$width};" : null !!}
            "
            @class([
                'rounded-full overflow-hidden' => $isRounded(),
                $ring . ' ring-white dark:ring-gray-900' => $imageCount > 1,
            ])
        >
            @if (\Awcodes\Curator\is_media_resizable($item->type))
                @php
                    $img_width = $width ? (int)$width : null;
                    $img_height = $height ? (int)$height : null;

                    if ($resolution) {
                        $img_width *= $resolution;
                        $img_height *= $resolution;
                    }
                @endphp
                <img
                    src="{{ $item->getSignedUrl([
                        'w' => $img_width,
                        'h' => $img_height,
                        'fit' => 'crop',
                        'fm' => 'webp'
                    ]) }}"
                    alt="{{ $item->alt }}"
                    style="
                        {!! $height !== null ? "height: {$height};" : null !!}
                        {!! $width !== null ? "width: {$width};" : null !!}
                    "
                    @class([
                        'max-w-none' => $height && ! $width,
                        'object-cover object-center' => ($isRounded() || $width || $height)
                    ])
                    {{ $getExtraImgAttributeBag() }}
                />
            @elseif (str($item->type)->contains('svg'))
                <img
                    src="{{ Storage::url($item->path) }}"
                    alt="{{ $item->alt }}"
                    style="
                        {!! $height !== null ? "height: {$height};" : "height: 2rem;" !!}
                        {!! $width !== null ? "width: {$width};" : null !!}
                    "
                    class="h-full w-auto"
                    {{ $getExtraImgAttributeBag() }}
                />
            @else
                <x-curator::document-image
                    :label="$item->name"
                    icon-size="md"
                    :type="$item->type"
                    :extension="$item->ext"
                    class="h-full w-full"
                />
            @endif
        </div>
        @endforeach
    @elseif($defaultImageUrl)
        <img
                src="{{ $defaultImageUrl }}"
                {{
					$getExtraImgAttributeBag()
						->class([
							'max-w-none object-cover object-center',
							'rounded-full' => $isCircular,
							$ringClasses,
						])
						->style([
							"height: {$height}" => $height,
							"width: {$width}" => $width,
						])
				}}
        />
    @endif
</div>
