<?php
declare(strict_types=1);

namespace Boctulus\Simplerest\tests;

require_once __DIR__ . '/../../src/framework/Libs/Strings.php';
if (!trait_exists(\Boctulus\Simplerest\Core\Traits\ExceptionHandler::class, false)) {
    require_once __DIR__ . '/../../src/framework/Traits/ExceptionHandler.php';
}
require_once __DIR__ . '/../../src/framework/Traits/QueryBuilderTrait.php';

use Boctulus\Simplerest\Core\Traits\QueryBuilderTrait;
use PHPUnit\Framework\TestCase;

final class MultilineNullBindingHarness
{
    public const EXECUTION_MODE_NORMAL = 0;
    use QueryBuilderTrait;

    public function prepareNullPredicates(string $sql): string
    {
        $this->w_vals = [null, null];
        $this->conn = new class {
            public function prepare(string $sql): object
            {
                return (object) ['sql' => $sql];
            }
        };
        return $this->bind($sql)->sql;
    }
}

final class QueryBuilderMultilineNullTest extends TestCase
{
    public function test_binding_inlines_null_predicates_before_preparing_multiline_sql(): void
    {
        $sql = "SELECT id FROM items\nWHERE deleted_at IS ?\nAND archived_at IS NOT ?";
        $prepared = (new MultilineNullBindingHarness())->prepareNullPredicates($sql);
        $this->assertSame(
            "SELECT id FROM items\nWHERE deleted_at IS NULL\nAND archived_at IS NOT NULL",
            $prepared
        );
    }
}
