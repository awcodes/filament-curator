<?php

namespace Awcodes\Curator\Resources\MediaResource;

use Exception;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use Awcodes\Curator\CuratorPlugin;
use Filament\Actions\CreateAction;
use Illuminate\Support\Facades\Gate;
use Filament\Resources\Pages\ListRecords;
use Awcodes\Curator\Actions\MultiUploadAction;

class ListMedia extends ListRecords
{
    public string $layoutView;

    public function mount(): void
    {
        parent::mount();
        $this->layoutView = CuratorPlugin::get()->getDefaultListView();
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

    public static function getResource(): string
    {
        return CuratorPlugin::get()->getResource();
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
            MultiUploadAction::make()
                ->authorize(function () {
                    $model = $this->getModel();
                    if (! Gate::getPolicyFor($model)) {
                        return true;
                    }
                    return Gate::allows('create', $model);
                }),
            CreateAction::make()
                ->label(fn (): string => trans('filament-actions::create.single.label', ['label' => CuratorPlugin::get()->getLabel()])),
        ];
    }
}
