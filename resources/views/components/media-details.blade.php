@php
$record = $getRecord();
$labelClasses = 'text-sm font-medium leading-4 text-gray-700 dark:text-gray-300';
$dataClasses = 'mt-2';
$dimensions = '-';
if (filled($record) && ($record->width && $record->height)) {
    $dimensions = $record->width . ' x ' . $record->height;
}
@endphp

<div>
    <dl class="grid md:grid-cols-3 gap-6">
        <div>
            <dt class="{{ $labelClasses }}">
                {{ __('filament-curator::resource.labels.uploaded_on') }}
            </dt>
            <dd class="{{ $dataClasses }}">
                {{ filled($record) ? $record->created_at->format('M j, Y') : '-' }}
            </dd>
        </div>
        <div>
            <dt class="{{ $labelClasses }}">
                {{ __('filament-curator::resource.labels.file_type') }}
            </dt>
            <dd class="{{ $dataClasses }}">
                {{ filled($record) ? $record->type : '-' }}
            </dd>
        </div>
        <div>
            <dt class="{{ $labelClasses }}">
                {{ __('filament-curator::resource.labels.file_size') }}
            </dt>
            <dd class="{{ $dataClasses }}">
                {{ filled($record) ? $record->sizeForHumans() : '-' }}
            </dd>
        </div>
        <div>
            <dt class="{{ $labelClasses }}">
                {{ __('filament-curator::resource.labels.dimensions') }}
            </dt>
            <dd class="{{ $dataClasses }}">
                {{ $dimensions }}
            </dd>
        </div>
        <div>
            <dt class="{{ $labelClasses }}">
                {{ __('filament-curator::resource.labels.disk') }}
            </dt>
            <dd class="{{ $dataClasses }}">
                {{ filled($record) ? $record->disk : '-' }}
            </dd>
        </div>
        <div>
            <dt class="{{ $labelClasses }}">
                {{ __('filament-curator::resource.labels.directory') }}
            </dt>
            <dd class="{{ $dataClasses }}">
                {{ filled($record) ? $record->directory : '-' }}
            </dd>
        </div>
        <div class="md:col-span-3">
            <dt class="{{ $labelClasses }}">
                {{ __('filament-curator::resource.labels.public_id') }}
            </dt>
            <dd class="{{ $dataClasses }}">
                {{ filled($record) ? $record->public_id : '-' }}
            </dd>
        </div>
        <div class="md:col-span-3">
            <dt class="{{ $labelClasses }}">
                {{ __('filament-curator::resource.labels.file_url') }}
            </dt>
            <dd class="{{ $dataClasses }}">
                @if (filled($record))
                    <div class="flex items-end justify-between gap-6">
                        <span>{{ $record->url }}</span>
                        <button
                                type="button"
                                class="text-sm flex-shrink-0 flex items-center gap-2"
                                x-data="{
                                    showMessage: false,
                                    toggleMessage: function() {
                                        this.showMessage = true;
                                        setTimeout(() => {
                                            this.showMessage = false;
                                        }, 1000)
                                    },
                                    handleCopy: function(subject) {
                                        navigator.clipboard.writeText(subject)
                                    }
                                }"
                                x-on:click="handleCopy('{{ $record->url }}'); toggleMessage();"
                        >
                            <span x-show="! showMessage" class="filament-link">@svg('heroicon-s-clipboard-copy', 'w-4 h-4')</span>
                            <span x-show="showMessage" class="text-success-500 font-bold" style="display:none;">@svg('heroicon-s-check-circle', 'w-4 h-4')</span>
                            <span x-show="! showMessage" class="filament-link">Copy URL</span>
                            <span x-show="showMessage" class="text-success-500 font-bold" style="display:none;">Copied!</span>
                        </button>
                    </div>
                @else
                -
                @endif
            </dd>
        </div>
    </dl>
</div>
