<?php

declare(strict_types=1);

use Boctulus\Simplerest\Core\Libs\Schema;
use PHPUnit\Framework\TestCase;

/**
 * Regression: Schema::default('texto') used to emit DEFAULT texto (invalid SQL).
 */
final class SchemaDefaultLiteralTest extends TestCase
{
    /** @return array<string, array{string, string}> */
    public static function values(): array
    {
        return [
            'plain string'          => ['texto', "'texto'"],
            'embedded quote'        => ["O'Brien", "'O''Brien'"],
            'date string'           => ['2024-01-01', "'2024-01-01'"],
            'already quoted'        => ["'ya'", "'ya'"],
            'integer string'        => ['30', '30'],
            'decimal string'        => ['-1.5', '-1.5'],
            'NULL keyword'          => ['NULL', 'NULL'],
            'timestamp keyword'     => ['current_timestamp', 'current_timestamp'],
            'timestamp function'    => ['current_timestamp()', 'current_timestamp()'],
            'function call'         => ['NOW()', 'NOW()'],
            'parenthesized call'    => ['(UUID())', '(UUID())'],
        ];
    }

    /** @dataProvider values */
    public function testStringDefaultsRenderAsSql(string $input, string $expected): void
    {
        self::assertSame($expected, Schema::defaultLiteral($input));
    }
}
