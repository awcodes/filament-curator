@props([
    'prefix' => null,
    'suffix' => null,
])
<label class="flex items-center w-full border border-gray-300 dark:border-gray-700 rounded-lg shadow-sm bg-gray-100 dark:bg-gray-800 text-sm">
    @if ($prefix)
        <span class="w-20 flex-shrink-0 self-stretch flex items-center justify-center px-2">{{ $prefix }}</span>
    @endif
    <input
        @class([
            "text-sm block w-full transition duration-75 border-none focus:border-primary-500 focus:ring-1 focus:ring-inset focus:ring-primary-500 disabled:opacity-70 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500",
            "!rounded-l-lg" => ! $prefix,
            "!rounded-r-lg" => ! $suffix,
        ])
        {{ $attributes->except(['prefix', 'suffix']) }}
    />
    @if ($suffix)
        <span class="w-16 self-stretch flex items-center justify-center px-2">{{ $suffix }}</span>
    @endif
</label>
