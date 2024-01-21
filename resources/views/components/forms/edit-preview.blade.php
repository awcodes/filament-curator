@props([
    'file' => null,
    'actions' => [],
])

@if ($file)
<x-dynamic-component :component="$getFieldWrapperView()">
    <div class="flex justify-center border border-gray-300 rounded dark:border-gray-700 h-48 flex-shrink-0 relative">
        <x-curator::display
            :item="$file"
            :src="glide()->getUrl($file['path'], ['h' => 192, 'fit' => 'contain', 'fm' => 'webp'])"
            icon-classes="size-24"
            :controls="true"
            :player="true"
            class="max-h-full max-w-full object-contain"
        />

        <div class="absolute top-0 right-0 flex bg-gray-900 divide-x divide-gray-700 rounded-bl-lg shadow-md">
            @foreach ($actions as $action)
                {{ ($action)(['item' => $file]) }}
            @endforeach
        </div>
    </div>
</x-dynamic-component>
@endif