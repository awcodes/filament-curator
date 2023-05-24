@php
    $items = $getMedia();
@endphp

<div
    {{ $attributes->merge($getExtraAttributes())->class([
        'curator-column px-4 py-3',
        'flex items-center -space-x-2' => count($items) > 1,
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
                'ring-2 ring-white dark:ring-gray-800' => count($items) > 1,
            ])
        >
            @if (app('curator')->isResizable($item->ext))
                <img
                    src="{{ $item->getSignedUrl(['w' => $width, 'h' => $height, 'fit' => 'crop', 'fm' => 'webp']) }}"
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
