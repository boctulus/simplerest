<?php

namespace Boctulus\Simplerest\tests;

use Boctulus\Simplerest\Core\Libs\Config;
use Boctulus\Simplerest\Core\Libs\DB;
use Boctulus\Simplerest\Core\Traits\QueryBuilderTrait;
use PHPUnit\Framework\TestCase;

final class WhereQuotingTraitHarness
{
	public const EXECUTION_MODE_NORMAL   = 0;
	public const EXECUTION_MODE_SIMULATE = 1;
	public const EXECUTION_MODE_PREVIEW  = 2;
	public const DECIMAL_AS_STRING = false;

    use QueryBuilderTrait;

    public function whereClauses(): array
    {
        return array_map('trim', $this->where);
    }

    public function whereVars(): array
    {
        return $this->w_vars;
    }
}

/**
 * Regression: a column named after a reserved word (`key`) broke WHERE because
 * identifiers were emitted unquoted.
 */
final class QueryBuilderWhereIdentifierQuotingTest extends TestCase
{
    private ?string $previousConnection = null;
    private array $previousConfig = [];

    // Config and DB state is injected directly: no application config files or database are needed.
    protected function setUp(): void
    {
        $data = new \ReflectionProperty(Config::class, 'data');
        $this->previousConfig = $data->getValue();
        $data->setValue(null, array_merge($this->previousConfig, [
            'db_connections' => ['quoting_test' => ['driver' => 'mysql']],
        ]));

        $connection = new \ReflectionProperty(DB::class, 'current_id_conn');
        $this->previousConnection = $connection->getValue();
        $connection->setValue(null, 'quoting_test');
    }

    protected function tearDown(): void
    {
        (new \ReflectionProperty(DB::class, 'current_id_conn'))->setValue(null, $this->previousConnection);
        (new \ReflectionProperty(Config::class, 'data'))->setValue(null, $this->previousConfig);
    }

    public function test_reserved_word_column_is_quoted(): void
    {
        $qb = (new WhereQuotingTraitHarness())->where(['key', 'abc']);

        $this->assertSame(['`key` = ?'], $qb->whereClauses());
        $this->assertSame(['key'], $qb->whereVars(), 'validation and type detection need the bare name');
    }

    public function test_qualified_column_is_quoted_per_part(): void
    {
        $qb = (new WhereQuotingTraitHarness())->where(['products.key', 'x']);

        $this->assertSame(['`products`.`key` = ?'], $qb->whereClauses());
    }

    public function test_in_condition_is_quoted(): void
    {
        $qb = (new WhereQuotingTraitHarness())->where([['key', ['a', 'b'], 'IN']]);

        $this->assertStringStartsWith('`key` IN (', $qb->whereClauses()[0]);
    }

    public function test_expressions_are_left_unchanged(): void
    {
        $qb = (new WhereQuotingTraitHarness())->where(['LOWER(name)', 'x']);

        $this->assertSame(['LOWER(name) = ?'], $qb->whereClauses());
    }
}
