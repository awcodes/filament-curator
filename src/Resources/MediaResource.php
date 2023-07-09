<?php

namespace Awcodes\Curator\Resources;

use Awcodes\Curator\Components\Forms\CuratorEditor;
use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use Exception;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class MediaResource extends Resource
{
    public static function getModel(): string
    {
        return config('curator.media_model');
    }

    public static function getModelLabel(): string
    {
        return config('curator.model_label');
    }

    public static function getPluralModelLabel(): string
    {
        return config('curator.model_plural_label');
    }

    public static function getNavigationLabel(): string
    {
        return Str::title(static::getPluralModelLabel()) ?? Str::title(static::getModelLabel());
    }

    public static function getNavigationIcon(): string
    {
        return config('curator.navigation_icon');
    }

    public static function getNavigationSort(): ?int
    {
        return config('curator.navigation_sort');
    }

    public static function getNavigationGroup(): ?string
    {
        return config('curator.navigation_group');
    }

    public static function shouldRegisterNavigation(): bool
    {
        return config('curator.should_register_navigation');
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
                                    ->required(),
                            ]),
                        Forms\Components\Tabs::make('image')
                            ->hiddenOn('create')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make(__('curator::forms.sections.preview'))
                                    ->schema([
                                        Forms\Components\ViewField::make('preview')
                                            ->view('curator::components.forms.preview')
                                            ->hiddenLabel()
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
                                            ->hiddenLabel()
                                            ->reorderable(false)
                                            ->itemLabel(fn($state): ?string => $state['curation']['key'] ?? null)
                                            ->collapsible()
                                            ->schema([
                                                CuratorEditor::make('curation')
                                                    ->hiddenLabel()
                                                    ->buttonLabel(__('curator::forms.curations.button_label'))
                                                    ->required()
                                                    ->lazy(),
                                            ]),
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
                                    ->hiddenLabel()
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
                Session::get('tableLayout')
                    ? static::getDefaultGridTableColumns()
                    : static::getDefaultTableColumns(),
            ))
            ->actions([
                config('curator.table_has_icon_actions')
                    ? Tables\Actions\EditAction::make()->iconButton()
                    : Tables\Actions\EditAction::make(),
                config('curator.table_has_icon_actions')
                    ? Tables\Actions\DeleteAction::make()->iconButton()
                    : Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->contentGrid(function () {
                if (Session::get('tableLayout')) {
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
                ->icons([
                    'heroicon-o-server',
                    'heroicon-o-cloud' => fn($state): bool => in_array($state, config('curator.cloud_disks')),
                ])
                ->colors([
                    'secondary',
                    'success' => fn($state): bool => in_array($state, config('curator.cloud_disks')),
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
                ->hint(fn(): HtmlString => new HtmlString('<a href="https://www.w3.org/WAI/tutorials/images/decision-tree" class="filament-link" target="_blank">' . __('curator::forms.fields.alt_hint') . '</a>')),
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
            ->hiddenLabel()
            ->preserveFilenames(config('curator.should_preserve_filenames'))
            ->maxWidth(config('curator.max_width'))
            ->minSize(config('curator.min_size'))
            ->maxSize(config('curator.max_size'))
            ->acceptedFileTypes(config('curator.accepted_file_types'))
            ->disk(config('curator.disk_name'))
            ->directory(config('curator.directory'))
            ->pathGenerator(config('curator.path_generator'))
            ->visibility(config('curator.visibility'))
            ->maxFiles(1)
            ->panelAspectRatio('24:9');
    }
}
