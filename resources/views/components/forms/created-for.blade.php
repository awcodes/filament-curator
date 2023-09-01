<div>
    @foreach(config('curator.created_for') as $resource)
        <p class="font-semibold mb-2 flex items-center gap-2 text-secondary-600 text-lg">
            <x-filament::icon
                :icon="$resource['resource']::getNavigationIcon()"
                class="h-5 w-5 text-gray-500 dark:text-gray-400"
            />
            {{ $resource['resource']::getNavigationLabel() }}
        </p>
        <ul class="list-disc ml-5">
            @foreach($resource['resource']::getModel()::where($resource['component'], $getRecord()->id)->get() as $items)
                <li>
                    <a target="_blank" href="{{ $resource['resource']::getUrl('edit',['record'=>$items->id]) }}">
                        {{ $items->{$resource['title']} }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endforeach
</div>
