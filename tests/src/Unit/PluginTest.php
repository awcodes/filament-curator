<?php

declare(strict_types=1);

use Awcodes\Curator\CuratorPlugin;
use Filament\Facades\Filament;

enum TestNavigationGroup: string
{
    case Media = 'Media';
}

enum TestNavigationIcon: string
{
    case Photo = 'heroicon-o-photo';
}

beforeEach(function () {
    $this->panel = Filament::getCurrentOrDefaultPanel();
});

it('can register the plugin', function () {
    $this->panel
        ->plugins([
            CuratorPlugin::make(),
        ]);

    expect(Filament::getPlugin('awcodes/curator'))->toBeInstanceOf(CuratorPlugin::class);
});

it('sets curations config', function (bool|Closure $condition) {
    $this->panel
        ->plugins([
            CuratorPlugin::make()
                ->curations($condition),
        ]);

    expect(Filament::getPlugin('awcodes/curator')->supportsCurations())->toBeFalse();
})->with([
    false,
    fn () => false,
]);

it('sets file swap config', function (bool|Closure $condition) {
    $this->panel
        ->plugins([
            CuratorPlugin::make()
                ->fileSwap($condition),
        ]);

    expect(Filament::getPlugin('awcodes/curator')->supportsFileSwap())->toBeFalse();
})->with([
    false,
    fn () => false,
]);

it('sets labels config', function (
    string|Closure $label,
    string|Closure $pluralLabel,
) {
    $this->panel
        ->plugins([
            CuratorPlugin::make()
                ->label($label)
                ->pluralLabel($pluralLabel),
        ]);

    $instance = Filament::getPlugin('awcodes/curator');
    expect($instance->getLabel())->toBe('test')
        ->and($instance->getPluralLabel())->toBe('tests');
})->with([
    ['test', 'tests'],
    [fn () => 'test', fn () => 'tests'],
]);

it('sets navigation config', function (
    string|Closure $group,
    string|Closure $icon,
    int|Closure $sort,
    bool|Closure $badge
) {
    $this->panel
        ->plugins([
            CuratorPlugin::make()
                ->navigationGroup($group)
                ->navigationIcon($icon)
                ->navigationSort($sort)
                ->showBadge($badge),
        ]);

    $instance = Filament::getPlugin('awcodes/curator');

    expect($instance->getNavigationGroup())->toBe('test')
        ->and($instance->getNavigationIcon())->toBe('heroicon-o-collection')
        ->and($instance->getNavigationSort())->toBe(1)
        ->and($instance->shouldShowBadge())->toBeTrue();
})->with([
    ['test', 'heroicon-o-collection', 1, true],
    [fn () => 'test', fn () => 'heroicon-o-collection', fn () => 1, fn () => true],
]);

it('sets navigation group with a backed enum', function () {
    $this->panel
        ->plugins([
            CuratorPlugin::make()
                ->navigationGroup(TestNavigationGroup::Media),
        ]);

    expect(Filament::getPlugin('awcodes/curator')->getNavigationGroup())
        ->toBe(TestNavigationGroup::Media);
});

it('sets navigation icon with a backed enum', function () {
    $this->panel
        ->plugins([
            CuratorPlugin::make()
                ->navigationIcon(TestNavigationIcon::Photo),
        ]);

    expect(Filament::getPlugin('awcodes/curator')->getNavigationIcon())
        ->toBe(TestNavigationIcon::Photo);
});

it('sets navigation sort with a closure', function () {
    $this->panel
        ->plugins([
            CuratorPlugin::make()
                ->navigationSort(fn () => 5),
        ]);

    expect(Filament::getPlugin('awcodes/curator')->getNavigationSort())->toBe(5);
});

it('sets register navigation config', function (bool|Closure $condition) {
    $this->panel
        ->plugins([
            CuratorPlugin::make()
                ->registerNavigation($condition),
        ]);

    expect(Filament::getPlugin('awcodes/curator')->shouldRegisterNavigation())->toBeFalse();
})->with([
    false,
    fn () => false,
]);
