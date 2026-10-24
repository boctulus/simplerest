<?php

declare(strict_types=1);

use Doctrine\Inflector\InflectorFactory;
use PHPUnit\Framework\TestCase;

/**
 * SubResourceHandler names pivot relations with doctrine/inflector's
 * pluralize(), so those names are keys of the subresource JSON. The fixture
 * holds the output of doctrine/inflector 2.1.0 for every table name in the
 * SimpleRest and GoProp schemas plus every English rule; an upgrade within
 * ^2.0 that changes any of them fails here before it reaches API consumers.
 */
final class PivotAliasInflectorTest extends TestCase
{
    public function testPluralizeKeepsTheRecordedPivotAliases(): void
    {
        $fixture = json_decode((string) file_get_contents(__DIR__ . '/fixtures/pivot-alias-plurals.json'), true, 512, JSON_THROW_ON_ERROR);
        $inflector = InflectorFactory::create()->build();

        $mismatches = [];
        foreach ($fixture['cases'] as $word => $expected) {
            $word = (string) $word;
            $actual = $inflector->pluralize($word);
            if ($actual !== $expected) {
                $mismatches[] = "$word: expected '$expected', got '$actual'";
            }
        }

        self::assertGreaterThan(600, count($fixture['cases']));
        self::assertSame([], $mismatches, $fixture['source']);
    }

    public function testSubResourceHandlerPluralizesPivotAliases(): void
    {
        $source = (string) file_get_contents(dirname(__DIR__, 2) . '/src/framework/Traits/SubResourceHandler.php');

        self::assertStringContainsString('use Doctrine\\Inflector\\InflectorFactory;', $source);
        self::assertMatchesRegularExpression('/\$relationshipName\s*=\s*\$inflector->pluralize\(\$tb\);/', $source);
        self::assertMatchesRegularExpression("/'alias'\s*=>\s*\\\$relationshipName/", $source);
    }
}
