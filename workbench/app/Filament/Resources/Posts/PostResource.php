<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Resources\Posts;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Components\Forms\RichEditor\AttachCuratorMediaPlugin;
use Awcodes\Curator\Components\Tables\CuratorColumn;
use BackedEnum;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Workbench\App\Filament\Resources\Posts\Pages\CreatePost;
use Workbench\App\Filament\Resources\Posts\Pages\EditPost;
use Workbench\App\Filament\Resources\Posts\Pages\ListPosts;
use Workbench\App\Models\Post;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedDocumentText;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),
            CuratorPicker::make('featured_image_id')
                ->label('Featured image')
                ->relationship('featuredImage', 'id'),
            CuratorPicker::make('gallery')
                ->relationship('gallery', 'name')
                ->multiple()
                ->listDisplay(fn (): bool => (bool) config('curator_testing.picker_list_display', false))
                ->orderColumn('order'),
            RichEditor::make('content')
                ->toolbarButtons([
                    ['bold', 'italic', 'link'],
                    ['attachCuratorMedia'],
                ])
                ->plugins([AttachCuratorMediaPlugin::make()]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('featuredImage'))
            ->columns([
                CuratorColumn::make('featuredImage')->label('Image'),
                TextColumn::make('title')->searchable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosts::route('/'),
            'create' => CreatePost::route('/create'),
            'edit' => EditPost::route('/{record}/edit'),
        ];
    }
}
