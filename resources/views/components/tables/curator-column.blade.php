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
                @php
                    $urlBuilder = \League\Glide\Urls\UrlBuilderFactory::create('/curator/', config('app.key'));
                    $url = $urlBuilder->getUrl($item->path, ['w' => $width, 'h' => $height, 'fit' => 'crop', 'fm' => 'webp']);
                @endphp
                <img
                    src="{{ $url }}"
                    alt="{{ $item->alt }}"
                    style="
                        {!! $height !== null ? "height: {$height};" : null !!}
                        {!! $width !== null ? "width: {$width};" : null !!}
                    "
                    @class(['object-cover object-center' => $isRounded() || $width || $height])
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
