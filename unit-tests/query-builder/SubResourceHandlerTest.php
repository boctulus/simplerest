<?php

namespace Boctulus\Simplerest\tests;

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../app.php';

use Boctulus\Simplerest\Core\Libs\DB;
use PHPUnit\Framework\TestCase;

class SubResourceHandlerTest extends TestCase
{
    public function test_direct_many_to_one_join_uses_schema_endpoints(): void
    {
        $sql = DB::table('properties', null, false)
            ->connectTo(['subdivisions'])
            ->dontExec()
            ->get();

        $this->assertStringContainsString(
            '__subdivision.id=properties.subdivision_id',
            $sql
        );
    }

    public function test_direct_one_to_many_join_uses_schema_endpoints_and_filters_soft_deletes(): void
    {
        $sql = DB::table('properties', null, false)
            ->connectTo(['property_co_owners'])
            ->dontExec()
            ->get();

        $this->assertStringContainsString(
            '__property_co_owners.property_id=properties.id AND deleted_at IS NULL',
            $sql
        );
        $this->assertStringNotContainsString(
            '__property_co_owners.id=properties.id',
            $sql
        );
    }
}
