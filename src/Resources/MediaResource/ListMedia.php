<?php

namespace RocketFirm\Curator\Resources\MediaResource;

use RocketFirm\Curator\Resources\MediaResource;
use Exception;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    protected function getTitle(): string
    {
        return Str::headline(app('curator')->getPluralResourceLabel());
    }

    /**
     * @throws Exception
     */
    protected function getActions(): array
    {
        return array_merge(
            [
                Action::make('toggle-table-view')
                    ->color('secondary')
                    ->label(function (): string {
                        return Session::get('tableLayout') ? __('curator::tables.actions.toggle_table_list') : __('curator::tables.actions.toggle_table_grid');
                    })
                    ->icon(function (): string {
                        return Session::get('tableLayout') ? 'heroicon-s-view-list' : 'heroicon-s-view-grid';
                    })
                    ->action(function (): void {
                        Session::put('tableLayout', ! Session::get('tableLayout'));
                    }),
            ],
            parent::getActions(),
        );
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableContentGrid(): ?array
    {
        if (app('curator')->shouldTableHaveGridLayout()) {
            return [
                'md' => 2,
                'lg' => 3,
                'xl' => 4,
            ];
        }

        return null;
    }

    protected function getDefaultTableRecordsPerPageSelectOption(): int
    {
        return 12;
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [6, 12, 24, 48, -1];
    }
}
