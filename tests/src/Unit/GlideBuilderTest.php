<?php

use Awcodes\Curator\Glide\GlideBuilder;
use function Awcodes\Curator\glide_builder;

it('can return proper array from Glide builder', function () {
    $builder = GlideBuilder::make()->width(200)->height(200)->quality(80)->format('webp');

    expect($builder->toArray())
        ->toBeArray()
        ->toMatchArray([
            'w' => 200,
            'h' => 200,
            'q' => 80,
            'fm' => 'webp',
        ]);
});

it('can return proper query string from Glide builder', function () {
    $builder = GlideBuilder::make()->width(200)->height(200)->quality(80)->format('webp');

    expect($builder->toQueryString())
        ->toContain('w=200')
        ->toContain('h=200')
        ->toContain('q=80')
        ->toContain('fm=webp');
});

it('can return proper array from Glide builder helper', function () {
    $builder = glide_builder()->width(200)->height(200)->quality(80)->format('webp');

    expect($builder->toArray())
        ->toBeArray()
        ->toMatchArray([
            'w' => 200,
            'h' => 200,
            'q' => 80,
            'fm' => 'webp',
        ]);
});

it('can return proper query string from Glide builder helper', function () {
    $builder = glide_builder()->width(200)->height(200)->quality(80)->format('webp');

    expect($builder->toQueryString())
        ->toContain('w=200')
        ->toContain('h=200')
        ->toContain('q=80')
        ->toContain('fm=webp');
});