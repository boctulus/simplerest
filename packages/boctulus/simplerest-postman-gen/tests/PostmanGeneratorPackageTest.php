<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\PostmanGenerator;
use PHPUnit\Framework\TestCase;

final class PostmanGeneratorPackageTest extends TestCase
{
    public function testPostmanGeneratorKeepsItsLegacyClassName(): void
    {
        self::assertTrue(class_exists(PostmanGenerator::class));
    }
}
