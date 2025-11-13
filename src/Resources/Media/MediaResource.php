<?php

declare(strict_types=1);

namespace Awcodes\Curator\Resources\Media;

use Awcodes\Curator\CuratorPlugin;
use Awcodes\Curator\Models\Media;
use Awcodes\Curator\Resources\Media\Pages\CreateMedia;
use Awcodes\Curator\Resources\Media\Pages\EditMedia;
use Awcodes\Curator\Resources\Media\Pages\ListMedia;
use Awcodes\Curator\Resources\Media\Schemas\MediaForm;
use Awcodes\Curator\Resources\Media\Tables\MediaTable;
use Exception;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;

class MediaResource extends Resource
{
    public static function getModel(): string
    {
        return Config::get('curator.model')
            ?? Media::class;
    }

    public static function isScopedToTenant(): bool
    {
        return Config::get('curator.features.tenancy.enabled')
            ?? static::$isScopedToTenant;
    }

    public static function getTenantOwnershipRelationshipName(): string
    {
        return Config::get('curator.features.tenancy.relationship_name')
            ?? Filament::getTenantOwnershipRelationshipName();
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
        return Str::title(static::getPluralModelLabel())
            ?? Str::title(static::getModelLabel());
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
        return CuratorPlugin::get()->shouldShowBadge()
            ? (Filament::hasTenancy() && Config::get('curator.features.tenancy.enabled'))
                ? number_format(static::getEloquentQuery()
                    ->where(Config::get('curator.features.tenancy.relationship_name').'_id', Filament::getTenant()->id)
                    ->count())
                : number_format(self::getModel()::count())
            : null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return CuratorPlugin::get()->shouldRegisterNavigation();
    }

    /** @throws Exception */
    public static function form(Schema $schema): Schema
    {
        return MediaForm::configure($schema);
    }

    /** @throws Exception */
    public static function table(Table $table): Table
    {
        return MediaTable::configure($table);
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
