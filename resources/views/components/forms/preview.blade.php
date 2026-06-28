@php
    $record = $getRecord();
@endphp

<x-curator::display :item="$record" :src="$record?->large_url" icon-classes="h-24"/>
