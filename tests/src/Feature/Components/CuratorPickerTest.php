<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Forms\CuratorPicker;

test('isMultiple returns false by default', function () {
    $field = CuratorPicker::make('media');

    expect($field->isMultiple())->toBeFalse();
});

test('isMultiple returns true after multiple(true)', function () {
    $field = CuratorPicker::make('media')->multiple(true);

    expect($field->isMultiple())->toBeTrue();
});

test('isMultiple evaluates closure', function () {
    $field = CuratorPicker::make('media')->multiple(fn () => true);

    expect($field->isMultiple())->toBeTrue();
});

test('isConstrained returns false by default', function () {
    $field = CuratorPicker::make('media');

    expect($field->isConstrained())->toBeFalse();
});

test('isConstrained returns true after constrained(true)', function () {
    $field = CuratorPicker::make('media')->constrained(true);

    expect($field->isConstrained())->toBeTrue();
});

test('isLimitedToDirectory returns false by default', function () {
    $field = CuratorPicker::make('media');

    expect($field->isLimitedToDirectory())->toBeFalse();
});

test('isLimitedToDirectory returns true after limitToDirectory(true)', function () {
    $field = CuratorPicker::make('media')->limitToDirectory(true);

    expect($field->isLimitedToDirectory())->toBeTrue();
});

test('isTenantAware returns false by default when tenancy is disabled in config', function () {
    $field = CuratorPicker::make('media');

    expect($field->isTenantAware())->toBeFalse();
});

test('isTenantAware returns true after tenantAware(true)', function () {
    $field = CuratorPicker::make('media')->tenantAware(true);

    expect($field->isTenantAware())->toBeTrue();
});

test('shouldLazyLoad returns false by default', function () {
    $field = CuratorPicker::make('media');

    expect($field->shouldLazyLoad())->toBeFalse();
});

test('shouldLazyLoad returns true after lazyLoad(true)', function () {
    $field = CuratorPicker::make('media')->lazyLoad(true);

    expect($field->shouldLazyLoad())->toBeTrue();
});

test('shouldDisplayAsList returns false by default', function () {
    $field = CuratorPicker::make('media');

    expect($field->shouldDisplayAsList())->toBeFalse();
});

test('shouldDisplayAsList returns true after listDisplay(true)', function () {
    $field = CuratorPicker::make('media')->listDisplay(true);

    expect($field->shouldDisplayAsList())->toBeTrue();
});

test('getMaxItems returns null by default', function () {
    $field = CuratorPicker::make('media');

    expect($field->getMaxItems())->toBeNull();
});

test('getMaxItems returns set value', function () {
    $field = CuratorPicker::make('media')->maxItems(5);

    expect($field->getMaxItems())->toBe(5);
});

test('getDefaultPanelSort returns desc by default', function () {
    $field = CuratorPicker::make('media');

    expect($field->getDefaultPanelSort())->toBe('desc');
});

test('getDefaultPanelSort returns custom value', function () {
    $field = CuratorPicker::make('media')->defaultPanelSort('asc');

    expect($field->getDefaultPanelSort())->toBe('asc');
});

test('hasRelationship returns false when no relationship configured', function () {
    $field = CuratorPicker::make('media');

    expect($field->hasRelationship())->toBeFalse();
});
