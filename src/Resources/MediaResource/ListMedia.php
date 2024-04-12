<?php

namespace Awcodes\Curator\Resources\MediaResource;

use Awcodes\Curator\Actions\MultiUploadAction;
use Awcodes\Curator\CuratorPlugin;
use Awcodes\Curator\Resources\MediaResource;
use Exception;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListMedia extends ListRecords
{
    protected static string $resource = MediaResource::class;

    public string $layoutView = 'grid';

    public function mount(): void
    {
        parent::mount();

        $this->layoutView = config('curator.resource.default_layout');
    }

    protected $listeners = [
        'changeLayoutView' => 'changeLayoutView',
        'layoutViewChanged' => '$refresh',
    ];

    public function changeLayoutView(): void
    {
        $this->layoutView = $this->layoutView === 'list' ? 'grid' : 'list';
        $this->dispatch('layoutViewChanged', $this->layoutView);
    }

    public function getTitle(): string
    {
        return Str::headline(CuratorPlugin::get()->getPluralLabel());
    }

    /**
     * @throws Exception
     */
    public function getHeaderActions(): array
    {
        return [
            Action::make('toggle-table-view')
                ->color('gray')
                ->label(function (): string {
                    return $this->layoutView === 'grid'
                        ? trans('curator::tables.actions.toggle_table_list')
                        : trans('curator::tables.actions.toggle_table_grid');
                })
                ->icon(function (): string {
                    return $this->layoutView === 'grid'
                        ? 'heroicon-s-queue-list'
                        : 'heroicon-s-squares-2x2';
                })
                ->action(function ($livewire): void {
                    $livewire->dispatch('changeLayoutView');
                }),
            MultiUploadAction::make(),
            CreateAction::make()
                ->label(fn (): string => trans('filament-actions::create.single.label', ['label' => CuratorPlugin::get()->getLabel()])),
        ];
    }
}
