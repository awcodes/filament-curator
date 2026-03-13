<?php

declare(strict_types=1);

use Awcodes\Curator\Components\Tables\CuratorColumn;

test('getResolution returns null by default', function () {
    $column = CuratorColumn::make('media');

    expect($column->getResolution())->toBeNull();
});

test('getResolution returns integer after resolution(200)', function () {
    $column = CuratorColumn::make('media')->resolution(200);

    expect($column->getResolution())->toBe(200);
});

test('getResolution evaluates closure', function () {
    $column = CuratorColumn::make('media')->resolution(fn () => 300);

    expect($column->getResolution())->toBe(300);
});
