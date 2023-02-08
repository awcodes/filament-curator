<div
    {{ $attributes->merge($getExtraAttributes())->class(['curator-column pb-[165px] rounded-t-xl overflow-hidden']) }}
>
    @php
        $record = $getRecord();
    @endphp

    <div class="absolute inset-0 h-[175px] z-0 rounded-t-xl overflow-hidden">
        @if (\Awcodes\Curator\Facades\Curator::isResizable($record->ext))
            <img
                src="/curator/{{ $record->path }}?w=640&h=320&fit=crop&fm=webp"
                alt="{{ $record->alt }}"
                class="object-cover h-full w-full"
            />
        @else
            <x-curator::document-image
                :label="$record->filename"
                icon-size="lg"
                :type="$record->type"
            />
        @endif
        <div
            class="absolute inset-x-0 bottom-0 flex items-center justify-between px-1.5 pt-10 pb-1.5 text-xs text-white bg-gradient-to-t from-black/80 to-transparent gap-3"
        >
            <p class="truncate">{{ $record->title ?? $record->name . '.' . $record->ext }}</p>
            <p class="flex-shrink-0">{{ $record->size_for_humans }}</p>
        </div>
    </div>
</div>
