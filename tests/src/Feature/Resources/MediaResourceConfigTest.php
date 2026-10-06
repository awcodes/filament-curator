<?php

declare(strict_types=1);

use Awcodes\Curator\Resources\Media\MediaResource;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Awcodes\Curator\Resources\Media\Pages\EditMedia;
use Awcodes\Curator\Resources\Media\Pages\ListMedia;
use Awcodes\Curator\Resources\Media\Schemas\MediaForm;
use Awcodes\Curator\Resources\Media\Tables\MediaTable;
use Filament\Schemas\Schema;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ConfiguredListMedia extends ListMedia {}

class ConfiguredCreateMedia extends CreateMedia {}

class ConfiguredEditMedia extends EditMedia {}

class ConfiguredMediaForm extends MediaForm
{
    public static bool $configured = false;

    public static function configure(Schema $schema): Schema
    {
        static::$configured = true;

        return $schema;
    }
}

class ConfiguredMediaTable extends MediaTable
{
    public static bool $configured = false;

    public static function configure(Table $table): Table
    {
        static::$configured = true;

        return $table;
    }
}

test('uses the page classes set in config', function () {
    config([
        'curator.resource.pages.index' => ConfiguredListMedia::class,
        'curator.resource.pages.create' => ConfiguredCreateMedia::class,
        'curator.resource.pages.edit' => ConfiguredEditMedia::class,
    ]);

    $pages = MediaResource::getPages();

    expect($pages['index']->getPage())->toBe(ConfiguredListMedia::class)
        ->and($pages['create']->getPage())->toBe(ConfiguredCreateMedia::class)
        ->and($pages['edit']->getPage())->toBe(ConfiguredEditMedia::class);
});

test('uses the form and table classes set in config', function () {
    config([
        'curator.resource.schemas.form' => ConfiguredMediaForm::class,
        'curator.resource.tables.table' => ConfiguredMediaTable::class,
    ]);

    MediaResource::form(Schema::make());
    MediaResource::table(Table::make(Mockery::mock(HasTable::class)));

    expect(ConfiguredMediaForm::$configured)->toBeTrue()
        ->and(ConfiguredMediaTable::$configured)->toBeTrue();
});
