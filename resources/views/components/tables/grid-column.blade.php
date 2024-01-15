@php
    $record = $getRecord();
@endphp

<div {{ $attributes->merge($getExtraAttributes())->class(['curator-grid-column absolute inset-0 rounded-t-xl overflow-hidden aspect-video']) }}>
    <div class="rounded-t-xl h-full overflow-hidden bg-gray-200 dark:bg-gray-700">
        <x-curator-glider
            :media="$record"
            width="640"
            height="320"
            fit="crop"
            format="webp"
            @class([
                'h-full',
                'w-auto' => str($record->mime)->contains('svg'),
                'object-cover w-full' => ! str($record->mime)->contains('svg'),
            ])
        />
        <div class="absolute inset-x-0 bottom-0 flex items-center justify-between px-1.5 pt-10 pb-1.5 text-xs text-white bg-gradient-to-t from-black/80 to-transparent gap-3">
            <p class="truncate">{{ $record->pretty_name }}</p>
            <p class="flex-shrink-0">{{ $record->size_for_humans }}</p>
        </div>
    </div>
</div>
