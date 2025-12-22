<?php

declare(strict_types=1);

namespace Awcodes\Curator\Resources\Media\Schemas;

use Awcodes\Curator\Components\Forms\CuratorEditor;
use Awcodes\Curator\Components\Forms\Uploader;
use Awcodes\Curator\CuratorPlugin;
use Awcodes\Curator\Facades\Curator;
use Exception;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class MediaForm
{
    /** @throws Exception */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(trans('curator::forms.sections.file'))
                            ->hiddenOn('edit')
                            ->schema([
                                App::make(config('curator.resource.schemas.form'))::getUploaderField()
                                    ->required(),
                            ]),
                        Tabs::make('image')
                            ->hiddenOn('create')
                            ->tabs([
                                Tab::make(trans('curator::forms.sections.preview'))
                                    ->schema([
                                        ViewField::make('preview')
                                            ->view('curator::components.forms.preview')
                                            ->hiddenLabel()
                                            ->dehydrated(false)
                                            ->afterStateHydrated(function (ViewField $component, $state, $record): void {
                                                $component->state($record);
                                            }),
                                    ]),
                                Tab::make(trans('curator::forms.sections.curation'))
                                    ->visible(fn ($record): bool => is_media_resizable($record->ext) && CuratorPlugin::get()->supportsCurations())
                                    ->schema([
                                        Repeater::make('curations')
                                            ->label(trans('curator::forms.sections.curation'))
                                            ->hiddenLabel()
                                            ->reorderable(false)
                                            ->itemLabel(fn ($state): ?string => $state['curation']['key'] ?? null)
                                            ->collapsible()
                                            ->schema([
                                                CuratorEditor::make('curation')
                                                    ->hiddenLabel()
                                                    ->buttonLabel(trans('curator::forms.curations.button_label'))
                                                    ->required()
                                                    ->lazy(),
                                            ]),
                                    ]),
                                Tab::make(trans('curator::forms.sections.replace'))
                                    ->visible(fn () => CuratorPlugin::get()->supportsFileSwap())
                                    ->schema([
                                        App::make(config('curator.resource.schemas.form'))::getUploaderField()
                                            ->helperText(trans('curator::forms.sections.upload_new_helper')),
                                    ]),
                            ]),
                        Section::make(trans('curator::forms.sections.details'))
                            ->schema([
                                ViewField::make('details')
                                    ->view('curator::components.forms.details')
                                    ->hiddenLabel()
                                    ->dehydrated(false)
                                    ->columnSpan('full')
                                    ->afterStateHydrated(function ($component, $state, $record): void {
                                        $component->state($record);
                                    }),
                            ]),
                    ])
                    ->columnSpan([
                        'md' => 'full',
                        'lg' => 2,
                    ]),
                Group::make()
                    ->schema([
                        Section::make(trans('curator::forms.sections.meta'))
                            ->schema(App::make(config('curator.resource.schemas.form'))::getAdditionalInformationFormSchema()),
                    ])->columnSpan([
                        'md' => 'full',
                        'lg' => 1,
                    ]),
            ])->columns([
                'lg' => 3,
            ]);
    }

    /** @throws Exception */
    public static function getAdditionalInformationFormSchema(): array
    {
        return [
            TextInput::make('name')
                ->label(trans('curator::forms.fields.name'))
                ->hiddenOn('create')
                ->required()
                ->dehydrateStateUsing(function ($component, $state) {
                    $slugged = Str::slug($state);
                    $component->state($slugged);

                    return $slugged;
                }),
            TextInput::make('alt')
                ->label(trans('curator::forms.fields.alt'))
                ->hint(fn (): HtmlString => new HtmlString('<a href="https://www.w3.org/WAI/tutorials/images/decision-tree" class="filament-link text-primary-500 text-xs" target="_blank">'.trans('curator::forms.fields.alt_hint').'</a>')),
            TextInput::make('title')
                ->label(trans('curator::forms.fields.title')),
            Textarea::make('caption')
                ->label(trans('curator::forms.fields.caption'))
                ->rows(2),
            Textarea::make('description')
                ->label(trans('curator::forms.fields.description'))
                ->rows(2),
        ];
    }

    /** @throws Exception */
    public static function getUploaderField(): Uploader
    {
        return Uploader::make('file')
            ->acceptedFileTypes(Curator::getAcceptedFileTypes())
            ->directory(Curator::getDirectory())
            ->disk(Curator::getDiskName())
            ->hiddenLabel()
            ->minSize(Curator::getMinSize())
            ->maxFiles(1)
            ->maxSize(Curator::getMaxSize())
            ->panelAspectRatio('24:9')
            ->preserveFilenames(Curator::shouldPreserveFilenames())
            ->visibility(Curator::getVisibility())
            ->storeFileNamesIn('originalFilename')
            ->imageCropAspectRatio(Curator::getImageCropAspectRatio())
            ->imageResizeMode(Curator::getImageResizeMode())
            ->imageResizeTargetWidth(Curator::getImageResizeTargetWidth())
            ->imageResizeTargetHeight(Curator::getImageResizeTargetHeight());
    }
}
