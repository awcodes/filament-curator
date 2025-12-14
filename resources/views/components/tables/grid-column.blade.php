@php
    $record = $getRecord();
    $isSvg = curator()->isSvg($record->ext);
@endphp

<div {{ $attributes->merge($getExtraAttributes())->class(['curator-grid-column absolute inset-0 rounded-t-xl overflow-hidden']) }}>
    <div @class([
        'rounded-t-xl h-full overflow-hidden bg-gray-100 dark:bg-gray-950/50',
        'checkered' => $isSvg,
    ])>
        <x-curator::display
            :item="$record"
            :src="$record->mediumUrl"
            :lazy="true"
            icon-classes="size-24"
            x-on:click="toggleSelectedRecord('{{ $record->id }}')"
            @class([
                'h-full',
                'w-auto mx-auto p-2' => $isSvg,
                'w-full' => ! $isSvg,
            ])
        />
        <x-curator::display.info-overlay :label="$record->pretty_name" :size="$record->size" />
    </div>
</div>
