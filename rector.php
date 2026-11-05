<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector;
use Rector\ValueObject\PhpVersion;

/**
 * Only the deprecation-driven rules live here. Type declarations, strict_types
 * and PHP 8.3 syntax changes are reviewed in their own pull request.
 */
return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src/framework'])
    ->withSkip([__DIR__ . '/src/framework/Templates'])
    ->withPhpVersion(PhpVersion::PHP_84)
    ->withRules([
        ExplicitNullableParamTypeRector::class,
    ]);
