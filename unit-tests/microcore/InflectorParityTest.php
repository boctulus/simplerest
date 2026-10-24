<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\Inflector;
use PHPUnit\Framework\TestCase;

/**
 * SubResourceHandler names pivot relations with Inflector::pluralize(), which
 * replaced doctrine/inflector. The fixture holds the output of
 * doctrine/inflector 2.1.0 (the version locked before the extraction) for every
 * table name in the SimpleRest and GoProp schemas plus every English rule, so
 * the aliases seen by API consumers cannot drift.
 */
final class InflectorParityTest extends TestCase
{
    public function testPluralizeMatchesDoctrineInflector(): void
    {
        $fixture = json_decode((string) file_get_contents(__DIR__ . '/fixtures/doctrine-inflector-plural.json'), true, 512, JSON_THROW_ON_ERROR);

        $mismatches = [];
        foreach ($fixture['cases'] as $word => $expected) {
            $word = (string) $word;
            $actual = Inflector::pluralize($word);
            if ($actual !== $expected) {
                $mismatches[] = "$word: expected '$expected', got '$actual'";
            }
        }

        self::assertGreaterThan(600, count($fixture['cases']));
        self::assertSame([], $mismatches, $fixture['source']);
    }

    public function testPivotAliasesOfSingularAndIrregularTables(): void
    {
        self::assertSame('products', Inflector::pluralize('products'));
        self::assertSame('product_categories', Inflector::pluralize('product_category'));
        self::assertSame('statuses', Inflector::pluralize('status'));
        self::assertSame('people', Inflector::pluralize('person'));
        self::assertSame('children', Inflector::pluralize('child'));
        self::assertSame('news', Inflector::pluralize('news'));
    }
}
