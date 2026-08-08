<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

try {
    return RectorConfig::configure()
        ->withPaths([
            __DIR__ . '/src',
        ])
        ->withPreparedSets(
            deadCode: true,
            codeQuality: true,
            typeDeclarations: true,
            privatization: true,
            earlyReturn: true,
        )
        ->withPhpSets();
} catch (Rector\Exception\Configuration\InvalidConfigurationException $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
