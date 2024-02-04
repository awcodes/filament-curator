<?php

namespace Awcodes\Curator\Resources;

use Awcodes\Curator\Components\Forms\CuratorEditor;
use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Awcodes\Curator\CuratorPlugin;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use function Awcodes\Curator\is_media_resizable;

class MediaResource extends Resource
{
    public static function getModel(): string
    {
        return config('curator.model');
    }

    public static function getModelLabel(): string
    {
        return CuratorPlugin::get()->getLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return CuratorPlugin::get()->getPluralLabel();
    }

    public static function getNavigationLabel(): string
    {
        return Str::title(static::getPluralModelLabel()) ?? Str::title(static::getModelLabel());
    }

    public static function getNavigationIcon(): string
    {
        return CuratorPlugin::get()->getNavigationIcon();
    }

    public static function getNavigationSort(): ?int
    {
        return CuratorPlugin::get()->getNavigationSort();
    }

    public static function getNavigationGroup(): ?string
    {
        return CuratorPlugin::get()->getNavigationGroup();
    }

    public static function getNavigationBadge(): ?string
    {
        return CuratorPlugin::get()->getNavigationCountBadge() ?
            (Filament::hasTenancy() && Config::get('curator.is_tenant_aware')) ?
                static::getEloquentQuery()
                ->where(Filament::getTenantOwnershipRelationshipName() . '_id', Filament::getTenant()->id)
                ->count()
            : number_format(static::getModel()::count())
            : null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(trans('curator::forms.sections.file'))
                            ->hiddenOn('edit')
                            ->schema([
                                static::getUploaderField()
                                    ->required(),
                            ]),
                        Forms\Components\Tabs::make('image')
                            ->hiddenOn('create')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make(trans('curator::forms.sections.preview'))
                                    ->schema([
                                        Forms\Components\ViewField::make('preview')
                                            ->view('curator::components.forms.preview')
                                            ->hiddenLabel()
                                            ->dehydrated(false)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                $component->state($record);
                                            }),
                                    ]),
                                Forms\Components\Tabs\Tab::make(trans('curator::forms.sections.curation'))
                                    ->visible(fn($record) => is_media_resizable($record->ext))
                                    ->schema([
                                        Forms\Components\Repeater::make('curations')
                                            ->label(trans('curator::forms.sections.curation'))
                                            ->hiddenLabel()
                                            ->reorderable(false)
                                            ->itemLabel(fn($state): ?string => $state['curation']['key'] ?? null)
                                            ->collapsible()
                                            ->schema([
                                                CuratorEditor::make('curation')
                                                    ->hiddenLabel()
                                                    ->buttonLabel(trans('curator::forms.curations.button_label'))
                                                    ->required()
                                                    ->lazy(),
                                            ]),
                                    ]),
                                Forms\Components\Tabs\Tab::make(trans('curator::forms.sections.upload_new'))
                                    ->schema([
                                        static::getUploaderField()
                                            ->helperText(trans('curator::forms.sections.upload_new_helper')),
                                    ]),
                            ]),
                        Forms\Components\Section::make(trans('curator::forms.sections.details'))
                            ->schema([
                                Forms\Components\ViewField::make('details')
                                    ->view('curator::components.forms.details')
                                    ->hiddenLabel()
                                    ->dehydrated(false)
                                    ->columnSpan('full')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        $component->state($record);
                                    }),
                            ]),
                        Forms\Components\Section::make(trans('curator::forms.sections.exif'))
                            ->collapsed()
                            ->visible(fn($record) => $record && $record->exif)
                            ->schema([
                                Forms\Components\KeyValue::make('exif')
                                    ->hiddenLabel()
                                    ->dehydrated(false)
                                    ->addable(false)
                                    ->deletable(false)
                                    ->editableKeys(false)
                                    ->columnSpan('full'),
                            ]),
                    ])
                    ->columnSpan([
                        'md' => 'full',
                        'lg' => 2,
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(trans('curator::forms.sections.meta'))
                            ->schema(
                                static::getAdditionalInformationFormSchema()
                            ),
                    ])->columnSpan([
                        'md' => 'full',
                        'lg' => 1,
                    ]),
            ])->columns([
                'lg' => 3,
            ]);
    }

    /**
     * @throws Exception
     */
    public static function table(Table $table): Table
    {
        $livewire = $table->getLivewire();

        return $table
            ->columns(
                $livewire->layoutView === 'grid'
                    ? static::getDefaultGridTableColumns()
                    : static::getDefaultTableColumns(),
            )
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions(static::getBulkActions($livewire->layoutView))
            ->defaultSort('created_at', 'desc')
            ->contentGrid(function () use ($livewire) {
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
            ->paginationPageOptions([6, 12, 24, 48, 'all']);
    }

    public static function getPages(): array
    {
        return [
            'index' => MediaResource\ListMedia::route('/'),
            'create' => MediaResource\CreateMedia::route('/create'),
            'edit' => MediaResource\EditMedia::route('/{record}/edit'),
        ];
    }

    public static function getDefaultTableColumns(): array
    {
        return [
            CuratorColumn::make('url')
                ->label(trans('curator::tables.columns.url'))
                ->size(40),
            Tables\Columns\TextColumn::make('name')
                ->label(trans('curator::tables.columns.name'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('ext')
                ->label(trans('curator::tables.columns.ext'))
                ->sortable(),
            Tables\Columns\IconColumn::make('disk')
                ->label(trans('curator::tables.columns.disk'))
                ->icons([
                    'heroicon-o-server',
                    'heroicon-o-cloud' => fn($state): bool => in_array($state, config('curator.cloud_disks')),
                ])
                ->colors([
                    'gray',
                    'success' => fn($state): bool => in_array($state, config('curator.cloud_disks')),
                ]),
            Tables\Columns\TextColumn::make('directory')
                ->label(trans('curator::tables.columns.directory'))
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label(trans('curator::tables.columns.created_at'))
                ->date('Y-m-d')
                ->sortable(),
        ];
    }

    public static function getDefaultGridTableColumns(): array
    {
        return [
            Tables\Columns\Layout\View::make('curator::components.tables.grid-column'),
            Tables\Columns\TextColumn::make('name')
                ->label(trans('curator::tables.columns.name'))
                ->extraAttributes(['class' => 'hidden'])
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('ext')
                ->label(trans('curator::tables.columns.ext'))
                ->extraAttributes(['class' => 'hidden'])
                ->sortable(),
            Tables\Columns\TextColumn::make('directory')
                ->label(trans('curator::tables.columns.directory'))
                ->extraAttributes(['class' => 'hidden'])
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label(trans('curator::tables.columns.created_at'))
                ->extraAttributes(['class' => 'hidden'])
                ->sortable(),
        ];
    }

    public static function getAdditionalInformationFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label(trans('curator::forms.fields.name'))
                ->hiddenOn('create')
                ->required()
                ->dehydrateStateUsing(function ($component, $state) {
                    $slugged = Str::slug($state);
                    $component->state($slugged);

                    return $slugged;
                }),
            Forms\Components\TextInput::make('alt')
                ->label(trans('curator::forms.fields.alt'))
                ->hint(fn(): HtmlString => new HtmlString('<a href="https://www.w3.org/WAI/tutorials/images/decision-tree" class="filament-link text-primary-500" target="_blank">' . trans('curator::forms.fields.alt_hint') . '</a>')),
            Forms\Components\TextInput::make('title')
                ->label(trans('curator::forms.fields.title')),
            Forms\Components\Textarea::make('caption')
                ->label(trans('curator::forms.fields.caption'))
                ->rows(2),
            Forms\Components\Textarea::make('description')
                ->label(trans('curator::forms.fields.description'))
                ->rows(2),
        ];
    }

    public static function getUploaderField(): Uploader
    {
        return Uploader::make('file')
            ->acceptedFileTypes(config('curator.accepted_file_types'))
            ->directory(config('curator.directory'))
            ->disk(config('curator.disk'))
            ->hiddenLabel()
            ->minSize(config('curator.min_size'))
            ->maxFiles(1)
            ->maxSize(config('curator.max_size'))
            ->panelAspectRatio('24:9')
            ->pathGenerator(config('curator.path_generator'))
            ->preserveFilenames(config('curator.should_preserve_filenames'))
            ->visibility(config('curator.visibility'))
            ->storeFileNamesIn('originalFilename');
    }

    public static function getBulkActions(string $view): array
    {
        if ($view === 'list') {
            return [
                Tables\Actions\DeleteBulkAction::make(),
            ];
        }
        
        return [];
    }
}
