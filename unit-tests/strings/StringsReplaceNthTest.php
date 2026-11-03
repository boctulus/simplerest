<?php
declare(strict_types=1);

namespace Boctulus\Simplerest\tests;

// Exercise this checkout's source, independently of an installed Composer copy.
require_once __DIR__ . '/../../src/framework/Libs/Strings.php';

use Boctulus\Simplerest\Core\Libs\Strings;
use PHPUnit\Framework\TestCase;

final class StringsReplaceNthTest extends TestCase
{
    /** @dataProvider sqlCases */
    public function test_null_placeholder_replacement_handles_sql_line_endings(string $sql): void
    {
        $actual = Strings::replaceNth('?', 'NULL', $sql, 1);
        $this->assertSame(str_replace('?', 'NULL', $sql), $actual);
        $this->assertStringNotContainsString('IS ?', $actual);
        $this->assertStringNotContainsString('IS NOT ?', $actual);
    }

    public static function sqlCases(): array
    {
        return [
            'single line' => ['SELECT id FROM items WHERE deleted_at IS ?'],
            'LF before NULL' => ["SELECT id FROM items\nWHERE deleted_at IS ?"],
            'CRLF before NULL' => ["SELECT id FROM items\r\nWHERE deleted_at IS ?"],
            'multiple lines before NULL' => ["SELECT id\nFROM items\nWHERE deleted_at IS ?"],
            'LF before NOT NULL' => ["SELECT id FROM items\nWHERE deleted_at IS NOT ?"],
        ];
    }

    public function test_replacing_a_later_placeholder_preserves_other_bindings(): void
    {
        $sql = "SELECT id FROM items WHERE organization_id = ?\nAND deleted_at IS ?\nAND status = ?";
        $this->assertSame(
            "SELECT id FROM items WHERE organization_id = ?\nAND deleted_at IS NULL\nAND status = ?",
            Strings::replaceNth('?', 'NULL', $sql, 2)
        );
    }

    public function test_repeated_null_replacement_matches_query_builder_binding_loop(): void
    {
        $sql = "SELECT id FROM items\nWHERE deleted_at IS ?\nAND archived_at IS ?";
        $sql = Strings::replaceNth('?', 'NULL', $sql, 1);
        $sql = Strings::replaceNth('?', 'NULL', $sql, 1);
        $this->assertSame("SELECT id FROM items\nWHERE deleted_at IS NULL\nAND archived_at IS NULL", $sql);
    }

    public function test_missing_occurrence_preserves_the_input(): void
    {
        $this->assertSame("first\nsecond", Strings::replaceNth('?', 'NULL', "first\nsecond", 1));
        $this->assertSame('one ? only', Strings::replaceNth('?', 'NULL', 'one ? only', 2));
    }

    public function test_search_is_a_literal_substring(): void
    {
        $this->assertSame("a.b\nc-d", Strings::replaceNth('.', '-', "a.b\nc.d", 2));
    }
}
