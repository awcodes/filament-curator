<?php

namespace App\Filament\Resources\Curator;

use Filament\Resources\Form;
use Filament\Resources\Table;
use Filament\Resources\Resource;
use FilamentCurator\Models\Media;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Placeholder;
use FilamentCurator\Forms\Components\MediaUpload;
use FilamentCurator\Tables\Columns\ThumbnailColumn;
use App\Filament\Resources\Curator\MediaResource\Pages\EditMedia;
use App\Filament\Resources\Curator\MediaResource\Pages\ListMedia;
use App\Filament\Resources\Curator\MediaResource\Pages\CreateMedia;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;

class MediaResource extends Resource
{
    protected static ?string $model = Media::class;

    protected static ?string $navigationIcon = 'heroicon-o-photograph';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make('File')
                            ->hidden(function ($livewire) {
                                return $livewire instanceof EditMedia;
                            })
                            ->schema([
                                MediaUpload::make('filename')
                                    ->preserveFilenames(config('filament-curator.preserve_file_names'))
                                    ->disableLabel()
                                    ->maxWidth(5000)
                                    ->acceptedFileTypes(config('filament-curator.accepted_file_types'))
                                    ->directory(config('filament-curator.directory', 'images'))
                                    ->disk(config('filament-curator.disk', 'public'))
                                    ->required()
                                    ->maxFiles(1)
                                    ->panelAspectRatio('24:9')
                            ]),
                        Section::make('Preview')
                            ->hidden(function ($livewire) {
                                return $livewire instanceof CreateMedia;
                            })
                            ->schema([
                                ViewField::make('preview')
                                    ->view('filament-curator::components.media-preview')
                                    ->disableLabel()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        $component->state($record);
                                    }),
                            ]),
                        Section::make('Details')
                            ->schema([
                                Placeholder::make('uploaded_on')
                                    ->label('Uploaded on')
                                    ->content(fn ($record): string => $record ? $record->created_at->format('F j, Y') : '-'),
                                Placeholder::make('file_type')
                                    ->label('File Type')
                                    ->content(fn ($record): string => $record ? $record->type : '-'),
                                Placeholder::make('file_size')
                                    ->label('File Size')
                                    ->content(fn ($record): string => $record ? $record->sizeForHumans() : '-'),
                                Placeholder::make('dimensions')
                                    ->label('Dimensions')
                                    ->content(fn ($record): string => $record ? $record->width . ' x ' . $record->height : '-'),
                                Placeholder::make('disk')
                                    ->label('Disk')
                                    ->content(fn ($record): string => $record ? $record->disk : '-'),
                                Placeholder::make('directory')
                                    ->label('Directory')
                                    ->content(fn ($record): string => $record ? $record->directory : '-'),
                                Placeholder::make('public_id')
                                    ->label('Public Id')
                                    ->content(fn ($record): string => $record ? $record->public_id : '-')->columnSpan(['lg' => 4]),
                                Placeholder::make('file_url')
                                    ->label('File URL')
                                    ->content(fn ($record): string => $record ? $record->url : '-')->columnSpan(['lg' => 4]),
                            ])->columns(['lg' => 4]),
                    ])
                    ->columnSpan([
                        'lg' => 'full',
                        'xl' => 2
                    ]),
                Group::make()
                    ->schema([
                        Section::make('Meta')
                            ->schema([
                                TextInput::make('alt')
                                    ->helperText('<span class="block -mt-1 text-xs"><a href="https://www.w3.org/WAI/tutorials/images/decision-tree" target="_blank" rel="noopener" class="underline text-primary-500 hover:text-primary-600 focus:text-primary-600">Learn how to describe the purpose of the image</a>. Leave empty if the image is purely decorative.</span>'),
                                TextInput::make('title'),
                                Textarea::make('caption')
                                    ->rows(2),
                                Textarea::make('description')
                                    ->rows(2),
                            ])
                    ])->columnSpan([
                        'lg' => 'full',
                        'xl' => 1,
                    ]),
            ])->columns([
                'lg' => 3,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ThumbnailColumn::make('thumbnail_url'),
                TextColumn::make('public_id')
                    ->searchable()
                    ->sortable(),
                IconColumn::make('disk')
                    ->options([
                        'heroicon-o-server',
                        'heroicon-o-cloud' => function ($state): bool {
                            return in_array($state, config('filament-curator.cloud_disks'));
                        },
                    ])
                    ->colors([
                        'secondary', 'success' => function ($state): bool {
                            return in_array($state, config('filament-curator.cloud_disks'));
                        },
                    ]),
                TextColumn::make('updated_at')
                    ->label('Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make()->iconButton(),
                DeleteAction::make()->iconButton(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedia::route('/'),
            'create' => CreateMedia::route('/create'),
            'edit' => EditMedia::route('/{record}/edit'),
        ];
    }
}
