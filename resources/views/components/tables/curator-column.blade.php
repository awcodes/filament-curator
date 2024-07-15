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
                @php
                    $img_width = $width ? (int)$width : null;
                    $img_height = $height ? (int)$height : null;

                    if ($resolution) {
                        $img_width *= $resolution;
                        $img_height *= $resolution;
                    }
                @endphp

                <x-curator::display
                    :item="$item"
                    :src="$item->thumbnailUrl"
                    :lazy="true"
                    icon-classes="size-6"
                    :width="$width"
                    :height="$height"
                    @class([
                        'bg-gray-100 dark:bg-gray-950/50',
                        'h-full w-auto' => str($item->type)->contains('svg'),
                        'max-w-none' => $height && ! $width,
                        'object-cover object-center' => ! str($item->type)->contains('svg') && ($isRounded() || $width || $height),
                        'w-full h-full' => curator()->isDocument($item->ext)
                    ])
                />
            </div>
        @endforeach
    @endif
</div>