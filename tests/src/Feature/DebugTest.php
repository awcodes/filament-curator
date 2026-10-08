<?php

declare(strict_types=1);

it('will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

it('will not use mbstring trim functions, which Laravel 11 and early 12 do not polyfill')
    ->expect(['mb_trim', 'mb_ltrim', 'mb_rtrim'])
    ->each->not->toBeUsed();
