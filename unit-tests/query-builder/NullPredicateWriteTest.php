<?php

namespace Boctulus\Simplerest\tests;

require_once __DIR__ . '/../../app.php';

use PHPUnit\Framework\TestCase;
use Boctulus\Simplerest\Core\Libs\DB;

/**
 * Predicados NULL en sentencias de escritura (UPDATE / DELETE).
 *
 * SELECT y DELETE duro pasan por bind(), que reemplaza el placeholder de los
 * valores NULL por el literal NULL. UPDATE arma y prepara su propio SQL, por lo
 * que whereNull()/whereNotNull() deben producir `IS NULL` / `IS NOT NULL` ahi
 * tambien: `IS ?` es sintacticamente invalido en MySQL y en PostgreSQL.
 *
 * Los casos de escritura usan dontExec(): se llega a prepare() (donde aparece el
 * error de sintaxis, con ATTR_EMULATE_PREPARES => false) pero nunca se ejecuta,
 * asi que ninguna fila es tocada.
 *
 * Ejecutar con: ./vendor/bin/phpunit unit-tests/query-builder/NullPredicateWriteTest.php
 */
class NullPredicateWriteTest extends TestCase
{
    public function setUp(): void
    {
        DB::setConnection('main');
    }

    protected function table()
    {
        return DB::table('files');
    }

    function test_select_where_null_compiles_is_null()
    {
        $sql = $this->table()->whereNull('organization_id')->dd();

        $this->assertStringContainsString('organization_id IS NULL', $sql);
        $this->assertStringNotContainsString('IS ?', $sql);
    }

    function test_select_where_null_executes()
    {
        $this->table()->whereNull('organization_id')->limit(1)->get();

        $this->assertStringContainsString('organization_id IS NULL', DB::getLog());
    }

    function test_update_with_where_null_compiles_is_null()
    {
        $q = $this->table()
            ->dontExec()
            ->where(['id' => '__non_existent_id__'])
            ->whereNull('organization_id');

        $q->update(['organization_id' => 1]);

        $sql = $q->getLastPrecompiledQuery();

        $this->assertStringContainsString('organization_id IS NULL', $sql);
        $this->assertStringNotContainsString('IS ?', $sql);
    }

    function test_update_with_where_not_null_compiles_is_not_null()
    {
        $q = $this->table()
            ->dontExec()
            ->where(['id' => '__non_existent_id__'])
            ->whereNotNull('organization_id');

        $q->update(['organization_id' => 1]);

        $sql = $q->getLastPrecompiledQuery();

        $this->assertStringContainsString('organization_id IS NOT NULL', $sql);
        $this->assertStringNotContainsString('IS NOT ?', $sql);
    }

    function test_soft_delete_with_where_null_compiles_is_null()
    {
        $q = $this->table()
            ->dontExec()
            ->where(['id' => '__non_existent_id__'])
            ->whereNull('organization_id');

        $q->delete(true);

        $this->assertStringNotContainsString('IS ?', $q->getLastPrecompiledQuery());
    }

    function test_hard_delete_with_where_null_compiles_is_null()
    {
        $this->table()
            ->dontExec()
            ->where(['id' => '__non_existent_id__'])
            ->whereNull('organization_id')
            ->delete(false);

        $this->assertStringContainsString('organization_id IS NULL', DB::getLog());
    }

    function test_update_setting_column_to_null_still_works()
    {
        $q = $this->table()
            ->dontExec()
            ->where(['id' => '__non_existent_id__']);

        $q->update(['organization_id' => null]);

        // `SET col = ?` con PDO::PARAM_NULL y `SET col = NULL` son ambos validos;
        // lo que se protege aca es que asignar NULL siga preparando sin error.
        $this->assertMatchesRegularExpression(
            '/SET\s+organization_id\s*=\s*(\?|NULL)/i',
            $q->getLastPrecompiledQuery()
        );
    }
}
