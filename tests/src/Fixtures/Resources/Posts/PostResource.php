<?php

declare(strict_types=1);

namespace Awcodes\Curator\Tests\Fixtures\Resources\Posts;

use Awcodes\Curator\Components\Forms\CuratorPicker;
use Awcodes\Curator\Tests\Fixtures\Models\Post;
use Awcodes\Curator\Tests\Fixtures\Resources\Posts\Pages\CreatePost;
use Awcodes\Curator\Tests\Fixtures\Resources\Posts\Pages\EditPost;
use Awcodes\Curator\Tests\Fixtures\Resources\Posts\Pages\ListPosts;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')->required(),
            CuratorPicker::make('gallery')
                ->relationship('gallery', 'name')
                ->multiple()
                ->orderColumn('order'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title'),
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
