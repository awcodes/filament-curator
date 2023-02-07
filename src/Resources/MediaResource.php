<?php

namespace Awcodes\Curator\Resources;

use Awcodes\Curator\Components\Forms\CuratorEditor;
use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Awcodes\Curator\Models\Media;
use Exception;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    public static function getModelLabel(): string
    {
        return app('curator')->getResourceLabel();
    }

    public static function getPluralModelLabel(): string
    {
        return app('curator')->getPluralResourceLabel();
    }

    public static function getNavigationLabel(): string
    {
        return static::getModelLabel();
    }

    protected static function getNavigationIcon(): string
    {
        return app('curator')->getNavigationIcon();
    }

    protected static function getNavigationSort(): ?int
    {
        return app('curator')->getNavigationSort();
    }

    protected static function getNavigationGroup(): ?string
    {
        return app('curator')->getNavigationGroup();
    }

    protected static function shouldRegisterNavigation(): bool
    {
        return app('curator')->shouldRegisterNavigation();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('curator::forms.sections.file'))
                            ->hiddenOn('edit')
                            ->schema([
                                static::getUploaderField()
                            ]),
                        Forms\Components\Tabs::make('image')
                            ->hiddenOn('create')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make(__('curator::forms.sections.preview'))
                                    ->schema([
                                        Forms\Components\ViewField::make('preview')
                                            ->view('curator::components.forms.preview')
                                            ->disableLabel()
                                            ->dehydrated(false)
                                            ->afterStateHydrated(function ($component, $state, $record) {
                                                $component->state($record);
                                            }),
                                    ]),
                                Forms\Components\Tabs\Tab::make(__('curator::forms.sections.curation'))
                                    ->visible(fn($record) => Str::of($record->type)->contains('image'))
                                    ->schema([
                                        Forms\Components\Repeater::make('curations')
                                            ->label(__('curator::forms.sections.curation'))
                                            ->disableLabel()
                                            ->itemLabel(fn ($state): ?string => $state['curation']['key'] ?? null)
                                            ->collapsible()
                                            ->schema([
                                                CuratorEditor::make('curation')
                                                    ->disableLabel()
                                                    ->buttonLabel(__('curator::forms.curations.button_label'))
                                                    ->required()
                                                    ->lazy(),
                                            ])
                                    ]),
                                Forms\Components\Tabs\Tab::make(__('curator::forms.sections.upload_new'))
                                    ->schema([
                                        static::getUploaderField()
                                            ->helperText(__('curator::forms.sections.upload_new_helper')),
                                    ]),
                            ]),
                        Forms\Components\Section::make(__('curator::forms.sections.details'))
                            ->schema([
                                Forms\Components\ViewField::make('details')
                                    ->view('curator::components.forms.details')
                                    ->disableLabel()
                                    ->dehydrated(false)
                                    ->columnSpan('full')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        $component->state($record);
                                    }),
                            ]),
                        Forms\Components\Section::make(__('curator::forms.sections.exif'))
                            ->collapsed()
                            ->visible(fn($record) => $record && $record->exif)
                            ->schema([
                                Forms\Components\KeyValue::make('exif')
                                    ->disableLabel()
                                    ->dehydrated(false)
                                    ->disableAddingRows()
                                    ->disableDeletingRows()
                                    ->disableEditingKeys()
                                    ->columnSpan('full'),
                            ]),
                    ])
                    ->columnSpan([
                        'md' => 'full',
                        'lg' => 2,
                    ]),
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('curator::forms.sections.meta'))
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
        return $table
            ->columns(array_merge(
                app('curator')->shouldTableHaveGridLayout()
                    ? static::getDefaultGridTableColumns()
                    : static::getDefaultTableColumns(),
            ))
            ->actions([
                app('curator')->shouldTableHaveIconActions() ? Tables\Actions\EditAction::make()->iconButton() : Tables\Actions\EditAction::make(),
                app('curator')->shouldTableHaveIconActions() ? Tables\Actions\DeleteAction::make()->iconButton() : Tables\Actions\DeleteAction::make(),
            ]);
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
                ->label(__('curator::tables.columns.url'))
                ->size(40),
            Tables\Columns\TextColumn::make('name')
                ->label(__('curator::tables.columns.name'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('ext')
                ->label(__('curator::tables.columns.ext'))
                ->sortable(),
            Tables\Columns\IconColumn::make('disk')
                ->label(__('curator::tables.columns.disk'))
                ->options([
                    'heroicon-o-server',
                    'heroicon-o-cloud' => fn ($state): bool => in_array($state, app('curator')->getCloudDisks()),
                ])
                ->colors([
                    'secondary',
                    'success' => fn ($state): bool => in_array($state, app('curator')->getCloudDisks()),
                ]),
            Tables\Columns\TextColumn::make('directory')
                ->label(__('curator::tables.columns.directory'))
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label(__('curator::tables.columns.created_at'))
                ->date('Y-m-d')
                ->sortable(),
        ];
    }

    public static function getDefaultGridTableColumns(): array
    {
        return [
            Tables\Columns\Layout\View::make('curator::components.tables.grid-column'),
            Tables\Columns\TextColumn::make('name')
                ->label(__('curator::tables.columns.name'))
                ->extraAttributes(['class' => 'hidden'])
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('ext')
                ->label(__('curator::tables.columns.ext'))
                ->extraAttributes(['class' => 'hidden'])
                ->sortable(),
            Tables\Columns\TextColumn::make('directory')
                ->label(__('curator::tables.columns.directory'))
                ->extraAttributes(['class' => 'hidden'])
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label(__('curator::tables.columns.created_at'))
                ->extraAttributes(['class' => 'hidden'])
                ->sortable(),
        ];
    }

    public static function getAdditionalInformationFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')
                ->label(__('curator::forms.fields.name'))
                ->hiddenOn('create')
                ->required()
                ->dehydrateStateUsing(function ($component, $state) {
                    $slugged = Str::slug($state);
                    $component->state($slugged);

                    return $slugged;
                }),
            Forms\Components\TextInput::make('alt')
                ->label(__('curator::forms.fields.alt'))
                ->hint(fn (): HtmlString => new HtmlString('<a href="https://www.w3.org/WAI/tutorials/images/decision-tree" class="filament-link" target="_blank">'.__('curator::forms.fields.alt_hint').'</a>')),
            Forms\Components\TextInput::make('title')
                ->label(__('curator::forms.fields.title')),
            Forms\Components\Textarea::make('caption')
                ->label(__('curator::forms.fields.caption'))
                ->rows(2),
            Forms\Components\Textarea::make('description')
                ->label(__('curator::forms.fields.description'))
                ->rows(2),
        ];
    }

    public static function getUploaderField(): Uploader
    {
        return Uploader::make('file')
            ->disableLabel()
            ->preserveFilenames(app('curator')->shouldPreserveFilenames())
            ->maxWidth(app('curator')->getMaxWidth())
            ->minSize(app('curator')->getMinSize())
            ->maxSize(app('curator')->getMaxSize())
            ->acceptedFileTypes(app('curator')->getAcceptedFileTypes())
            ->disk(app('curator')->getDiskName())
            ->directory(app('curator')->getDirectory())
            ->pathGenerator(app('curator')->getPathGenerator())
            ->visibility(app('curator')->getVisibility())
            ->maxFiles(1)
            ->panelAspectRatio('24:9');
    }
}
