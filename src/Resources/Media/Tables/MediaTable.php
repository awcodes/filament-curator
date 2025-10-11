<?php

declare(strict_types=1);

namespace Awcodes\Curator\Resources\Media\Tables;

use Awcodes\Curator\Components\Tables\CuratorColumn;
use Awcodes\Curator\Facades\Curator;
use Exception;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\Layout\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MediaTable
{
    /** @throws Exception */
    public static function configure(Table $table): Table
    {
        $livewire = $table->getLivewire();

        return $table
            ->columns(
                $livewire->layoutView === 'grid'
                    ? static::getDefaultGridTableColumns()
                    : static::getDefaultTableColumns(),
            )
            ->searchable(['title', 'caption', 'description'])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->contentGrid(function () use ($livewire): ?array {
                if ($livewire->layoutView === 'grid') {
                    return [
                        'md' => 2,
                        'lg' => 3,
                        'xl' => 4,
                    ];
                }

                return null;
            })
            ->defaultPaginationPageOption(12)
            ->paginationPageOptions([6, 12, 24, 48, 'all'])
            ->recordUrl(null);
    }

    /** @throws Exception */
    public static function getDefaultTableColumns(): array
    {
        return [
            CuratorColumn::make('url')
                ->label(trans('curator::tables.columns.url'))
                ->imageSize(40),
            TextColumn::make('name')
                ->label(trans('curator::tables.columns.name'))
                ->searchable()
                ->sortable(),
            TextColumn::make('ext')
                ->label(trans('curator::tables.columns.ext'))
                ->sortable(),
            TextColumn::make('size')
                ->label(trans('curator::tables.columns.size'))
                ->formatStateUsing(fn ($record): string => Curator::sizeForHumans($record->size))
                ->sortable(),
            TextColumn::make('dimensions')
                ->label(trans('curator::tables.columns.dimensions'))
                ->getStateUsing(fn ($record): ?string => $record->width ? $record->width.'x'.$record->height : null),
            TextColumn::make('disk')
                ->label(trans('curator::tables.columns.disk'))
                ->toggledHiddenByDefault()
                ->toggleable()
                ->sortable(),
            TextColumn::make('directory')
                ->label(trans('curator::tables.columns.directory'))
                ->toggledHiddenByDefault()
                ->toggleable()
                ->sortable(),
            TextColumn::make('created_at')
                ->label(trans('curator::tables.columns.created_at'))
                ->date('Y-m-d')
                ->sortable(),
        ];
    }

    /** @throws Exception */
    public static function getDefaultGridTableColumns(): array
    {
        return [
            View::make('curator::components.tables.grid-column'),
            TextColumn::make('name')
                ->label(trans('curator::tables.columns.name'))
                ->extraAttributes(['class' => 'hidden'])
                ->searchable()
                ->sortable(),
            TextColumn::make('ext')
                ->label(trans('curator::tables.columns.ext'))
                ->extraAttributes(['class' => 'hidden'])
                ->sortable(),
            TextColumn::make('directory')
                ->label(trans('curator::tables.columns.directory'))
                ->extraAttributes(['class' => 'hidden'])
                ->sortable(),
            TextColumn::make('created_at')
                ->label(trans('curator::tables.columns.created_at'))
                ->extraAttributes(['class' => 'hidden'])
                ->sortable(),
        ];
    }
}
