@props([
    'prefix' => null,
])
<label class="flex items-center w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-800 text-sm">
    @if ($prefix)
        <span class="w-20 flex-shrink-0 self-stretch flex items-center justify-center px-2">{{ $prefix }}</span>
    @endif
    <select
        @class([
            'block w-full !rounded-l-none transition duration-75 focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 rounded-lg shadow-sm bg-white text-sm border-none dark:text-white dark:focus:border-primary-500 dark:bg-gray-700',
            "!rounded-lg" => ! $prefix,
        ])
        {{ $attributes->except(['prefix', 'suffix']) }}
    >
        {{ $slot }}
    </select>
</label>
