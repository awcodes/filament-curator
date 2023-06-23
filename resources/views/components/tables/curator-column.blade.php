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

    $overlap = match ($overlap) {
        0 => 'space-x-0',
        2 => '-space-x-2',
        3 => '-space-x-3',
        4 => '-space-x-4',
        default => '-space-x-1',
    };
@endphp

<div
    {{ $attributes->merge($getExtraAttributes())->class([
        'curator-column px-4 py-3',
        $overlap . ' flex items-center' => $imageCount > 1,
    ]) }}
>
    @php
        $height = $getHeight();
        $width = $getWidth() ?? ($isRounded() ? $height : null);
    @endphp

    @if ($items)
        @foreach ($items as $item)
        <div style="
                {!! $height !== null ? "height: {$height};" : null !!}
                {!! $width !== null ? "width: {$width};" : null !!}
            "
            @class([
                'rounded-full overflow-hidden' => $isRounded(),
                $ring . ' ring-white dark:ring-gray-800' => $imageCount > 1,
            ])
        >
            @if (app('curator')->isResizable($item->ext))
                <img
                    src="{{ $item->getSignedUrl([
                        'w' => $width ? str_replace('px', '', $width) : $width,
                        'h' => $height ? str_replace('px', '', $height) : $height,
                        'fit' => 'crop',
                        'fm' => 'webp'
                    ]) }}"
                    alt="{{ $item->alt }}"
                    style="
                        {!! $height !== null ? "height: {$height};" : null !!}
                        {!! $width !== null ? "width: {$width};" : null !!}
                    "
                    @class([
                        'h-full w-auto' => str($item->type)->contains('svg'),
                        'object-cover object-center' => ! str($item->type)->contains('svg') && ($isRounded() || $width || $height)
                    ])
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
    @endif
</div>
