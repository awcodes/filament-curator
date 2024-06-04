@php
    $record = $getRecord();
@endphp

<div {{ $attributes->merge($getExtraAttributes())->class(['curator-grid-column absolute inset-0 rounded-t-xl overflow-hidden aspect-video']) }}>
    <div class="rounded-t-xl h-full overflow-hidden bg-gray-100 dark:bg-gray-950/50">
        <x-curator::display
            :item="$record"
            :src="$record->mediumUrl"
            :lazy="true"
            icon-classes="size-24"
            @class([
                'h-full',
                'w-auto mx-auto' => str($record->mime)->contains('svg'),
                'object-cover w-full' => ! str($record->mime)->contains('svg'),
            ])
        />
        <x-curator::display.info-overlay :label="$record->pretty_name" :size="$record->size" />
    </div>
</div>
